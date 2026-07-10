@extends('layouts.app_back')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, {{ $customer->m07_name }}</h2>
        <div>
            <a href="{{ route('customer.profile') }}" class="btn btn-outline-primary me-2">My Profile</a>
            <a href="{{ route('customer.logout') }}" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Wallet Balance</h5>
                    <h3>₹{{ $wallet ? number_format($wallet->tr02_balance - $wallet->tr02_hold_amount, 2) : '0.00' }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Samples Registered</h5>
                    <h3>{{ $samples->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>My Samples</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Reference ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Charges (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($samples as $sample)
                        <tr>
                            <td>{{ $sample->tr04_reference_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($sample->created_at)->format('d M, Y') }}</td>
                            <td>
                                @if($sample->tr04_progress == 'REGISTERED')
                                    <span class="badge bg-success">{{ $sample->tr04_progress }}</span>
                                @elseif($sample->tr04_progress == 'PENDING_PAYMENT')
                                    <span class="badge bg-warning text-dark">{{ $sample->tr04_progress }}</span>
                                @else
                                    <span class="badge bg-info">{{ $sample->tr04_progress }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($sample->tr04_total_charges + $sample->tr04_cgst + $sample->tr04_sgst + $sample->tr04_igst, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No samples found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
