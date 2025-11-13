<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RazorpayController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Show the payment page
     */
    public function checkout()
    {
        return view('payments.payment');
    }

    /**
     * Create Razorpay Order for Wallet Top-up
     */
    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:100',
                'customerId' => 'required'
            ]);

            $userId = Session::get('user_id');
            $amountInRupees = $request->input('amount');
            $amount = $amountInRupees * 100; // convert to paise

            // Create Razorpay order
            $order = $this->api->order->create([
                'receipt' => 'rcpt_' . Str::random(10),
                'amount' => $amount,
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            // Save order to DB
            $payment = Payment::create([
                'tr02_order_id' => $order['id'],
                'tr02_amount' => $amount,
                'tr02_status' => 'created',
                'm07_customer_id' => $request->customerId,
                'tr02_type' => 'wallet_topup',
                'created_at' => now()
            ]);

            Log::info('Razorpay Order Created', [
                'order_id' => $order['id'],
                'amount' => $amount,
                'user_id' => $request->customerId,
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $amount,
                'key' => config('services.razorpay.key'),
                'payment_id' => $payment->tr02_payment_id
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Order creation failed!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment and Credit Wallet
     */
    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'razorpay_payment_id' => 'required',
                'razorpay_order_id' => 'required',
                'razorpay_signature' => 'required'
            ]);

            $razorpayPaymentId = $request->input('razorpay_payment_id');
            $razorpayOrderId   = $request->input('razorpay_order_id');
            $razorpaySignature = $request->input('razorpay_signature');

            // Verify signature
            $generatedSignature = hash_hmac(
                'sha256',
                $razorpayOrderId . "|" . $razorpayPaymentId,
                config('services.razorpay.secret')
            );

            if ($generatedSignature !== $razorpaySignature) {
                Log::error('Payment Signature Verification Failed', [
                    'order_id' => $razorpayOrderId,
                    'payment_id' => $razorpayPaymentId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed!'
                ], 400);
            }

            DB::beginTransaction();

            // ✅ Fetch the payment record
            $payment = Payment::where('tr02_order_id', $razorpayOrderId)->first();

            if (!$payment) {
                throw new \Exception('Payment record not found');
            }

            // ✅ Always use the customer ID linked to this payment
            $customerId = $payment->m07_customer_id;

            // Update payment status
            $payment->update([
                'tr02_payment_t_id' => $razorpayPaymentId,
                'tr02_status' => 'paid',
                'tr02_payment_verified_at' => now()
            ]);

            // Lock or create the customer wallet
            $wallet = Wallet::where('m07_customer_id', $customerId)->lockForUpdate()->first();

            if (!$wallet) {
                $wallet = Wallet::create([
                    'tr02_wallet_uuid' => (string) Str::uuid(),
                    'm07_customer_id' => $customerId,
                    'tr02_balance' => 0,
                    'tr02_hold_amount' => 0,
                    'tr02_currency' => 'INR',
                    'tr02_status' => 'active'
                ]);
            }

            $amountInRupees = $payment->tr02_amount / 100;
            $balanceBefore = $wallet->tr02_balance;
            $wallet->tr02_balance += $amountInRupees;
            $wallet->save();

            // Create wallet transaction
            $transactionCount = WalletTransaction::count();
            $transaction = WalletTransaction::create([
                'tr03_transaction_uuid' => 'TXN-' . date('Y') . '-' . str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT),
                'tr02_wallet_id' => $wallet->tr02_wallet_id,
                'tr03_type' => 'credit',
                'tr03_amount' => $amountInRupees,
                'tr03_currency' => 'INR',
                'tr03_description' => 'Wallet Top-up via Razorpay',
                'tr03_payment_reference' => $razorpayPaymentId,
                'tr03_invoice_number' => 'RCP-' . date('Y') . '-' . str_pad(WalletTransaction::where('tr03_type', 'credit')->count() + 1, 4, '0', STR_PAD_LEFT),
                'tr03_balance_before' => $balanceBefore,
                'tr03_balance_after' => $wallet->tr02_balance,
                'tr03_status' => 'completed',
                'm07_created_by' => Auth::id() ?? $customerId, // whoever performed the action
                'tr03_payment_method' => 'Razorpay',
                'tr03_razorpay_order_id' => $razorpayOrderId,
                'tr03_razorpay_payment_id' => $razorpayPaymentId
            ]);

            // Link payment to wallet transaction
            $payment->update([
                'tr03_wallet_transaction_id' => $transaction->tr03_transaction_id
            ]);

            DB::commit();

            Log::info('Payment Verified and Wallet Credited', [
                'customer_id' => $customerId,
                'amount' => $amountInRupees,
                'transaction_id' => $transaction->tr03_transaction_uuid,
                'new_balance' => $wallet->tr02_balance
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully!',
                'transaction_id' => $transaction->tr03_transaction_uuid,
                'new_balance' => $wallet->tr02_balance,
                'amount_credited' => $amountInRupees
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment Verification Error: ' . $e->getMessage(), [
                'order_id' => $razorpayOrderId ?? 'N/A',
                'payment_id' => $razorpayPaymentId ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Webhook handler for Razorpay
     */
    public function webhook(Request $request)
    {
        try {
            $webhookSecret = config('services.razorpay.webhook_secret');
            $webhookSignature = $request->header('X-Razorpay-Signature');
            $webhookBody = $request->getContent();

            // Verify webhook signature
            $expectedSignature = hash_hmac('sha256', $webhookBody, $webhookSecret);

            if ($webhookSignature !== $expectedSignature) {
                Log::error('Webhook signature verification failed');
                return response()->json(['status' => 'error'], 400);
            }

            $event = $request->all();

            Log::info('Razorpay Webhook Received', ['event' => $event['event']]);

            switch ($event['event']) {
                case 'payment.authorized':
                    $this->handlePaymentAuthorized($event['payload']['payment']['entity']);
                    break;

                case 'payment.captured':
                    $this->handlePaymentCaptured($event['payload']['payment']['entity']);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($event['payload']['payment']['entity']);
                    break;

                case 'refund.created':
                    $this->handleRefundCreated($event['payload']['refund']['entity']);
                    break;
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle payment authorized event
     */
    private function handlePaymentAuthorized($paymentData)
    {
        Payment::where('tr02_order_id', $paymentData['order_id'])
            ->update([
                'tr02_payment_t_id' => $paymentData['id'],
                'tr02_status' => 'authorized'
            ]);

        Log::info('Payment Authorized', ['payment_id' => $paymentData['id']]);
    }

    /**
     * Handle payment captured event
     */
    private function handlePaymentCaptured($paymentData)
    {
        Payment::where('tr02_order_id', $paymentData['order_id'])
            ->update([
                'tr02_status' => 'captured'
            ]);

        Log::info('Payment Captured', ['payment_id' => $paymentData['id']]);
    }

    /**
     * Handle payment failed event
     */
    private function handlePaymentFailed($paymentData)
    {
        Payment::where('tr02_order_id', $paymentData['order_id'])
            ->update([
                'tr02_payment_t_id' => $paymentData['id'],
                'tr02_status' => 'failed',
                'tr02_failure_reason' => $paymentData['error_description'] ?? 'Payment failed'
            ]);

        Log::info('Payment Failed', [
            'payment_id' => $paymentData['id'],
            'reason' => $paymentData['error_description'] ?? 'Unknown'
        ]);
    }

    /**
     * Handle refund created event
     */
    private function handleRefundCreated($refundData)
    {
        // Handle refund logic here
        Log::info('Refund Created', ['refund_id' => $refundData['id']]);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($orderId)
    {
        try {
            $payment = Payment::where('tr02_order_id', $orderId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $payment->tr02_status,
                'amount' => $payment->tr02_amount / 100,
                'payment_id' => $payment->tr02_payment_t_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
