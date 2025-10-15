@extends('layouts.app_back')

@section('title', 'Wallet Management')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- Left Section -->
                        <div>
                            <h3 class="nk-block-title page-title mb-1">Wallet Management</h3>
                            <p class="text-soft mb-0">Manage your wallet balance and transactions</p>
                        </div>

                        <!-- Right Section -->
                        <div>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                                <em class="icon ni ni-caret-left-fill"></em>Back
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Wallet Balance Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Total Balance -->
                        <div class="col-md-4">
                            <div class="card card-bordered shadow-sm h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="title mb-1">Total Balance</h6>
                                            <span class="fs-6 text-muted">Overall wallet amount</span>
                                        </div>
                                        <em class="icon ni ni-wallet text-primary" style="font-size: 1.8rem;"></em>
                                    </div>
                                    <h3 class="amount mb-0 fw-bold text-primary" id="totalBalance">
                                        ₹{{ number_format($wallet->tr02_balance ?? 0, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Hold Amount -->
                        <div class="col-md-4">
                            <div class="card card-bordered shadow-sm h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="title mb-1">Hold Amount</h6>
                                            <span class="fs-6 text-muted">Funds on hold</span>
                                        </div>
                                        <em class="icon ni ni-lock text-warning" style="font-size: 1.8rem;"></em>
                                    </div>
                                    <h3 class="amount mb-0 fw-bold text-warning" id="holdAmount">
                                        ₹{{ number_format($wallet->tr02_hold_amount ?? 0, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Available Balance -->
                        <div class="col-md-4">
                            <div class="card card-bordered shadow-sm h-100">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="title mb-1">Available Balance</h6>
                                            <span class="fs-6 text-muted">Usable wallet funds</span>
                                        </div>
                                        <em class="icon ni ni-check-circle text-success" style="font-size: 1.8rem;"></em>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <h3 class="amount mb-0 fw-bold text-success" id="availableBalance">
                                            ₹{{ number_format(($wallet->tr02_balance ?? 0) - ($wallet->tr02_hold_amount ?? 0), 2) }}
                                        </h3>
                                        <a href="#"
                                            class="btn btn-outline-primary btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#topupModal">
                                            <em class="icon ni ni-plus"></em>
                                            <span>Add Money</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tabs -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#transactions">
                                    <em class="icon ni ni-list"></em><span>Transaction History</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pendingSamples">
                                    <em class="icon ni ni-clock"></em><span>Pending Samples</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Transaction History Tab -->
                            <div class="tab-pane active" id="transactions">
                                <div class="card-inner">
                                    <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                        <thead>
                                            <tr class="nk-tb-item nk-tb-head">
                                                <th class="nk-tb-col"><span class="sub-text">Transaction ID</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Date & Time</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Type</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Description</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Invoice</span></th>
                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Amount</span>
                                                </th>
                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Balance</span>
                                                </th>
                                                <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $transaction)
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col">
                                                        <span
                                                            class="tb-amount">{{ $transaction->tr03_transaction_uuid }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <span>{{ $transaction->created_at->format('d M Y') }}</span>
                                                        <span
                                                            class="text-soft">{{ $transaction->created_at->format('h:i A') }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        @if ($transaction->tr03_type === 'CREDIT')
                                                            <span class="badge badge-dot bg-success">Credit</span>
                                                        @elseif($transaction->tr03_type === 'DEBIT')
                                                            <span class="badge badge-dot bg-danger">Debit</span>
                                                        @elseif($transaction->tr03_type === 'HOLD')
                                                            <span class="badge badge-dot bg-warning">Hold</span>
                                                        @else
                                                            <span
                                                                class="badge badge-dot bg-info">{{ ucfirst($transaction->tr03_type) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <span class="tb-lead">{{ $transaction->tr03_description }}</span>
                                                        @if ($transaction->tr04_sample_registration_id)
                                                            <span class="text-soft d-block">Sample:
                                                                {{ $transaction->tr04_sample_registration_id }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        @if ($transaction->tr03_invoice_number)
                                                            <a href="#"
                                                                class="text-secondary">{{ $transaction->tr03_invoice_number }}</a>
                                                        @else
                                                            <span class="text-soft">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span
                                                            class="tb-amount {{ $transaction->tr03_type === 'CREDIT' ? 'text-success' : 'text-danger' }}">
                                                            {{ $transaction->tr03_type === 'CREDIT' ? '+' : '-' }}₹{{ number_format($transaction->tr03_amount, 2) }}
                                                        </span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span
                                                            class="tb-amount">₹{{ number_format($transaction->tr03_balance_after, 2) }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        @if ($transaction->tr03_status === 'COMPLETED')
                                                            <span class="text-success fw-bold">Completed</span>
                                                        @elseif($transaction->tr03_status === 'PENDING')
                                                            <span class="text-warning fw-bold">Pending</span>
                                                        @elseif($transaction->tr03_status === 'FAILED')
                                                            <span class="text-danger fw-bold">Failed</span>
                                                        @else
                                                            <span
                                                                class="text-secondary fw-bold">{{ ucfirst($transaction->tr03_status) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pending Samples Tab -->
                            <div class="tab-pane" id="pendingSamples">
                                <div class="card-inner">
                                    <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                        <thead>
                                            <tr class="nk-tb-item nk-tb-head">
                                                <th class="nk-tb-col"><span class="sub-text">Sample ID</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Sample info</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Registration Date</span>
                                                </th>
                                                <th class="nk-tb-col"><span class="sub-text">Hold Amount</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Invoice</span></th>
                                                <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                <th class="nk-tb-col nk-tb-col-tools text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pendingSamples as $sample)
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col">
                                                        <span class="tb-lead">{{ $sample->sample_id }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <span>{{ $sample->test_name }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <span>{{ \Carbon\Carbon::parse($sample->registration_date)->format('d M Y') }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <span
                                                            class="tb-amount text-warning">₹{{ number_format($sample->hold_amount, 2) }}</span>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <a href="#"
                                                            class="text-primary">{{ $sample->invoice_number }}</a>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        @if ($sample->status === 'VERIFIED')
                                                            <span class="text-info fw-bold">Ready for Reporting</span>
                                                        @elseif($sample->status === 'TESTED')
                                                            <span class="text-warning fw-bold">In Testing</span>
                                                        @else
                                                            <span
                                                                class="text-secondary fw-bold">{{ ucfirst($sample->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="nk-tb-col nk-tb-col-tools">
                                                        <ul class="nk-tb-actions gx-1">
                                                            @if ($sample->status === 'VERIFIED')
                                                                <li>
                                                                    <button
                                                                        class="btn btn-sm btn-primary process-reporting-btn"
                                                                        data-sample-id="{{ $sample->sample_id }}"
                                                                        data-hold-amount="{{ $sample->hold_amount }}"
                                                                        data-invoice="{{ $sample->invoice_number }}">
                                                                        <em class="icon ni ni-file-check"></em>
                                                                        <span>Report</span>
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Top Up Modal -->
    <div class="modal fade" id="topupModal" tabindex="-1" aria-labelledby="topupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topupModalLabel">Top Up Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="topupForm">
                        <div class="form-group">
                            <label class="form-label">Amount (INR)</label>
                            <div class="form-control-wrap">
                                <input type="number" class="form-control form-control-lg" id="topupAmount"
                                    name="amount" placeholder="Enter amount" min="1" required>
                            </div>
                            <div class="form-note">Minimum amount: ₹100</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quick Amount</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-primary w-100 quick-amount"
                                        data-amount="500">₹500</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-primary w-100 quick-amount"
                                        data-amount="1000">₹1000</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-primary w-100 quick-amount"
                                        data-amount="5000">₹5000</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <ul class="custom-control-group">
                                <li>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="paymentMethod"
                                            id="razorpay" value="razorpay" checked>
                                        <label class="custom-control-label" for="razorpay">
                                            <span class="d-flex align-items-center">
                                                <em class="icon ni ni-credit-card me-2"></em>
                                                <span>UPI / Card / Net Banking</span>
                                            </span>
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="proceedPayment">
                        <em class="icon ni ni-arrow-right"></em><span>Proceed to Payment</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Insufficient Balance Modal -->
    <div class="modal fade" id="insufficientBalanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <em class="icon ni ni-alert-circle me-2"></em>Insufficient Balance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <div class="alert-text">
                            Your wallet does not have sufficient funds to process this sample to reporting stage.
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label text-soft">Sample ID</label>
                                    <div class="form-control-plaintext" id="modalSampleId">—</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label text-soft">Invoice</label>
                                    <div class="form-control-plaintext" id="modalInvoice">—</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label text-soft">Required Amount</label>
                                    <div class="form-control-plaintext text-danger fw-bold" id="modalRequired">—</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label text-soft">Available Balance</label>
                                    <div class="form-control-plaintext" id="modalAvailable">—</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Shortage Amount:</strong>
                                        <strong class="text-danger" id="modalShortage">—</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Top-up Amount</label>
                        <input type="number" class="form-control form-control-lg" id="insufficientAmount"
                            placeholder="Enter amount" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="payNowInsufficient">
                        <em class="icon ni ni-wallet"></em><span>Pay Now</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        $(document).ready(function() {
            // Quick amount selection
            $('.quick-amount').on('click', function() {
                const amount = $(this).data('amount');
                $('#topupAmount').val(amount);
            });

            // Proceed to Payment
            $('#proceedPayment').on('click', function() {
                const amount = $('#topupAmount').val();

                if (!amount || amount < 100) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Please enter a valid amount (minimum ₹100)',
                    });
                    return;
                }

                initiateRazorpayPayment(amount);
            });

            // Process to Reporting
            $('.process-reporting-btn').on('click', function() {
                const sampleId = $(this).data('sample-id');
                const holdAmount = parseFloat($(this).data('hold-amount'));
                // const holdAmount = parseFloat($('#holdAmount').text().replace('₹', '').replace(',', ''));
                const invoice = $(this).data('invoice');
                const availableBalance = parseFloat($('#availableBalance').text().replace('₹', '').replace(
                    ',', ''));

                console.log(holdAmount);
                if (availableBalance < holdAmount) {
                    showInsufficientBalanceModal(sampleId, holdAmount, invoice, availableBalance);
                } else {
                    processToReporting(sampleId, holdAmount);
                }
            });

            // Pay Now from Insufficient Balance Modal
            $('#payNowInsufficient').on('click', function() {
                const amount = $('#insufficientAmount').val();

                if (!amount || amount < 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Please enter a valid amount',
                    });
                    return;
                }

                $('#insufficientBalanceModal').modal('hide');
                initiateRazorpayPayment(amount, true);
            });

            function initiateRazorpayPayment(amount, isInsufficient = false) {
                $.ajax({
                    url: '{{ route('razorpay.create.order') }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            openRazorpay(response, isInsufficient);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to create payment order',
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                        });
                    }
                });
            }

            function openRazorpay(orderData, isInsufficient) {
                const options = {
                    key: orderData.key,
                    amount: orderData.amount,
                    currency: 'INR',
                    name: '{{ config('app.name') }}',
                    description: 'Wallet Top-up',
                    order_id: orderData.order_id,
                    handler: function(response) {
                        verifyPayment(response, isInsufficient);
                    },
                    prefill: {
                        name: '{{ Session::get('name') ?? '' }}',
                        email: '{{ Session::get('email') ?? '' }}',
                        contact: '{{ Session::get('phone') ?? '' }}'
                    },
                    theme: {
                        color: '#6576ff'
                    },
                    modal: {
                        ondismiss: function() {
                            $('#topupModal').modal('hide');
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            }

            function verifyPayment(paymentResponse, isInsufficient) {
                $.ajax({
                    url: '{{ route('razorpay.verify.payment') }}',
                    method: 'POST',
                    data: {
                        razorpay_payment_id: paymentResponse.razorpay_payment_id,
                        razorpay_order_id: paymentResponse.razorpay_order_id,
                        razorpay_signature: paymentResponse.razorpay_signature,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#topupModal').modal('hide');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Successful!',
                                text: 'Your wallet has been topped up successfully.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Failed',
                                text: response.message || 'Payment verification failed',
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Payment verification failed. Please contact support.',
                        });
                    }
                });
            }

            function showInsufficientBalanceModal(sampleId, requiredAmount, invoice, availableBalance) {
                const shortage = requiredAmount - availableBalance;

                $('#modalSampleId').text(sampleId);
                $('#modalInvoice').text(invoice);
                $('#modalRequired').text('₹' + requiredAmount.toFixed(2));
                $('#modalAvailable').text('₹' + availableBalance.toFixed(2));
                $('#modalShortage').text('₹' + shortage.toFixed(2));
                $('#insufficientAmount').val(shortage.toFixed(2));

                $('#insufficientBalanceModal').modal('show');
            }

            function processToReporting(sampleId, holdAmount) {
                Swal.fire({
                    title: 'Confirm Processing',
                    text: `Process sample ${sampleId} to reporting stage?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Process',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('wallet.process.reporting') }}',
                            method: 'POST',
                            data: {
                                sample_id: sampleId,
                                hold_amount: holdAmount,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Sample processed to reporting stage successfully.',
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message ||
                                            'Failed to process sample',
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Something went wrong. Please try again.',
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
