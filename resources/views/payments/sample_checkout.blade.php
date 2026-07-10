@extends('layouts.app_back')

@section('content')
<div class="container mt-5">
    <div class="card w-50 mx-auto">
        <div class="card-header text-center bg-primary text-white">
            <h4>Sample Registration Payment</h4>
        </div>
        <div class="card-body text-center">
            <p><strong>Customer:</strong> {{ $customer->m07_name }}</p>
            <p><strong>Total Samples:</strong> {{ $samples->count() }}</p>
            <h3 class="text-success mb-4">Total Amount: ₹{{ number_format($totalAmount, 2) }}</h3>

            <button id="pay-btn" class="btn btn-lg btn-success w-100">Pay Now</button>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('pay-btn').onclick = function(e){
    e.preventDefault();
    
    // First, create the order
    fetch("{{ route('payment.create_sample_order') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            amount: {{ $totalAmount }},
            sampleIds: {!! json_encode($samples->pluck('tr04_sample_registration_id')) !!}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": "LIMS Portal",
                "description": "Sample Registration Payment",
                "order_id": data.order_id,
                "handler": function (response){
                    // Verify payment
                    fetch("{{ route('payment.verify_sample') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature
                        })
                    })
                    .then(res => res.json())
                    .then(verifyData => {
                        if (verifyData.success) {
                            alert("Payment Successful! Sample registered.");
                            window.location.href = "{{ url('/') }}"; // Redirect to portal/home
                        } else {
                            alert("Payment Verification Failed.");
                        }
                    });
                },
                "prefill": {
                    "name": "{{ $customer->m07_name }}",
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
        } else {
            alert("Error generating payment order.");
        }
    });
};
</script>
@endsection
