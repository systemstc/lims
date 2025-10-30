<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Razorpay Test Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body>
    <h2>Test Razorpay Payment</h2>
    <input type="number" id="amount" value="100" placeholder="Enter amount">
    <button id="payBtn">Pay Now</button>

    <script>
        document.getElementById('payBtn').addEventListener('click', async function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const amount = document.getElementById('amount').value;

            const res = await fetch("{{ route('razorpay.createOrder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    amount: amount
                })
            });

            const data = await res.json();
            if (!data.success) {
                alert("Error: " + data.message);
                return;
            }

            const options = {
                key: data.key,
                amount: data.amount,
                currency: 'INR',
                name: 'LIMS Payment',
                description: 'Test Transaction',
                order_id: data.order_id,
                handler: async function(response) {
                    const verifyRes = await fetch("{{ route('razorpay.verifyPayment') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(response)
                    });
                    const verifyData = await verifyRes.json();
                    alert(verifyData.message);
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        });
    </script>
</body>

</html>
