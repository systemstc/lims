@component('mail::message')
# Sample Registration Successful

Dear Customer,

Your sample has been successfully registered.

### 📌 Sample Details  
- **Reference ID:** {{ $registration->tr04_reference_id }}
- **Total Charges:** ₹{{ number_format($registration->tr04_total_charges, 2) }}
- **Expected Report Date:** {{ \Carbon\Carbon::parse($registration->tr04_expected_date)->format('d M, Y') }}

We will notify you once the report is ready.

Thanks,  
{{ config('app.name') }}
@endcomponent
