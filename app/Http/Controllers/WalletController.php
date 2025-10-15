<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\SampleRegistration;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class WalletController extends Controller
{
    /**
     * Display wallet dashboard
     */
    public function viewWallet($id)
    {
        $user = Session::get('customer_id') ? Session::get('customer_id')  : $id;

        // Get or create wallet for the user
        $wallet = Wallet::firstOrCreate(
            ['m07_customer_id' => $user],
            [
                'tr02_wallet_uuid' => (string) Str::uuid(),
                'tr02_balance' => 0,
                'tr02_hold_amount' => 0,
                'tr02_currency' => 'INR',
                'tr02_status' => 'active'
            ]
        );

        // Get recent transactions
        $transactions = WalletTransaction::where('tr02_wallet_id', $wallet->tr02_wallet_id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Get pending samples (samples with hold amount)
        $pendingSamples = SampleRegistration::where('m07_customer_id', $user)
            ->whereIn('tr04_progress', ['REGISTERED', 'ALLOTED', 'TESTED', 'REPORTED', 'VERIFIED'])
            ->with('holdTransaction')
            ->get()
            ->map(function ($sample) {
                return (object)[
                    'sample_id' => $sample->tr04_reference_id,
                    'test_name' => $sample->tr04_sample_description, // You can customize this
                    'registration_date' => $sample->created_at->format('Y-m-d'),
                    'hold_amount' => $sample->tr04_hold_amount ?? 0,
                    'invoice_number' => $sample->tr04_reference_id ?? 'N/A',
                    'status' => $sample->tr04_progress ?? 'Registered'
                ];
            });
        // dd($pendingSamples);
        return view('wallet.view_wallet', compact('wallet', 'transactions', 'pendingSamples'));
    }

    /**
     * Process sample to reporting stage
     */
    public function processToReporting(Request $request)
    {
        $request->validate([
            'sample_id' => 'required|string',
            'hold_amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $user = SampleRegistration::where('tr04_reference_id', $request->sample_id)->get('m07_customer_id');
            dd($user->m07_customer_id);
            $wallet = Wallet::where('tr02_user_id', $user->m07_customer_id)->lockForUpdate()->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            $sample = SampleRegistration::where('tr04_reference_id', $request->sample_id)
                ->where('tr04_created_by', $user->m07_customer_id)
                ->first();

            if (!$sample) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sample not found'
                ], 404);
            }

            // Check available balance
            $availableBalance = $wallet->tr02_balance - $wallet->tr02_hold_amount;
            $requiredAmount = $request->hold_amount;

            if ($availableBalance < $requiredAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance',
                    'required' => $requiredAmount,
                    'available' => $availableBalance,
                    'shortage' => $requiredAmount - $availableBalance
                ], 400);
            }

            // Release hold amount
            if ($sample->tr04_hold_transaction_id) {
                $holdTransaction = WalletTransaction::find($sample->tr04_hold_transaction_id);
                if ($holdTransaction) {
                    $holdTransaction->update(['tr03_status' => 'released']);
                }
            }

            // Deduct amount from wallet
            $balanceBefore = $wallet->tr02_balance;
            $wallet->tr02_balance -= $requiredAmount;
            $wallet->tr02_hold_amount -= $requiredAmount;
            $wallet->save();

            // Create debit transaction
            $transactionCount = WalletTransaction::count();
            $transaction = WalletTransaction::create([
                'tr03_transaction_uuid' => 'TXN-' . date('Y') . '-' . str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT),
                'tr03_wallet_id' => $wallet->tr02_wallet_id,
                'tr03_type' => 'debit',
                'tr03_amount' => $requiredAmount,
                'tr03_currency' => 'INR',
                'tr03_description' => 'Payment for Completed Test',
                'tr03_sample_registration_id' => $sample->tr04_sample_registration_id,
                'tr03_invoice_number' => $sample->tr04_reference_no,
                'tr03_balance_before' => $balanceBefore,
                'tr03_balance_after' => $wallet->tr02_balance,
                'tr03_status' => 'completed',
                'tr03_created_by' => $user->m07_customer_id
            ]);

            // Update sample status
            $sample->update([
                'tr04_status' => 'reporting',
                'tr04_payment_status' => 'paid'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sample processed to reporting stage successfully',
                'transaction_id' => $transaction->tr03_transaction_uuid,
                'new_balance' => $wallet->tr02_balance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to process sample: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create hold transaction when sample is registered
     */
    public function createHoldTransaction($sampleId, $sampleRegistrationId, $amount, $invoiceNumber)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $wallet = Wallet::where('tr02_user_id', $user->id)->lockForUpdate()->first();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Increase hold amount
            $balanceBefore = $wallet->tr02_balance;
            $wallet->tr02_hold_amount += $amount;
            $wallet->save();

            // Create hold transaction
            $transactionCount = WalletTransaction::count();
            $transaction = WalletTransaction::create([
                'tr03_transaction_uuid' => 'TXN-' . date('Y') . '-' . str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT),
                'tr03_wallet_id' => $wallet->tr02_wallet_id,
                'tr03_type' => 'hold',
                'tr03_amount' => $amount,
                'tr03_currency' => 'INR',
                'tr03_description' => 'Hold for Sample Registration',
                'tr03_sample_registration_id' => $sampleRegistrationId,
                'tr03_invoice_number' => $invoiceNumber,
                'tr03_balance_before' => $balanceBefore,
                'tr03_balance_after' => $wallet->tr02_balance,
                'tr03_status' => 'pending',
                'tr03_created_by' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'transaction_id' => $transaction->tr03_transaction_id,
                'hold_amount' => $amount
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get wallet balance
     */
    public function getBalance()
    {
        $user = Auth::user();
        $wallet = Wallet::where('tr02_user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'balance' => $wallet->tr02_balance,
            'hold_amount' => $wallet->tr02_hold_amount,
            'available_balance' => $wallet->tr02_balance - $wallet->tr02_hold_amount,
            'currency' => $wallet->tr02_currency
        ]);
    }

    /**
     * Get transaction history
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('tr02_user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        $query = WalletTransaction::where('tr03_wallet_id', $wallet->tr02_wallet_id);

        // Filter by type
        if ($request->has('type')) {
            $query->where('tr03_type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    /**
     * Download transaction statement
     */
    public function downloadStatement(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('tr02_user_id', $user->id)->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found');
        }

        $fromDate = $request->from_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? Carbon::now()->format('Y-m-d');

        $transactions = WalletTransaction::where('tr03_wallet_id', $wallet->tr02_wallet_id)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate PDF or Excel here
        // For now, returning view
        return view('wallet.statement', compact('wallet', 'transactions', 'fromDate', 'toDate'));
    }
}
