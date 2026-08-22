<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LaunchController extends Controller
{
    /**
     * Show the VIP Inauguration & Digital Launch Ceremony Screen
     */
    public function ceremony()
    {
        $isLaunched = Cache::get('lims_launched_status', false);
        $launchedAt = Cache::get('lims_launched_at', '22 August 2026, 06:30 PM IST');

        return view('launch.ceremony', compact('isLaunched', 'launchedAt'));
    }

    /**
     * Trigger digital launch event via AJAX
     */
    public function triggerLaunch(Request $request)
    {
        $timestamp = date('d F Y, h:i A') . ' IST';
        Cache::put('lims_launched_status', true);
        Cache::put('lims_launched_at', $timestamp);

        return response()->json([
            'status' => 'success',
            'message' => 'LIMS 2.0 has been officially inaugurated by Union Minister Shri Giriraj Singh!',
            'launched_at' => $timestamp,
            'dignitary' => 'Shri Giriraj Singh',
            'designation' => 'Hon’ble Minister of Textiles, Govt. of India'
        ]);
    }

    /**
     * Reset digital launch status for re-testing/rehearsals
     */
    public function resetLaunch(Request $request)
    {
        Cache::forget('lims_launched_status');
        Cache::forget('lims_launched_at');

        return response()->json([
            'status' => 'success',
            'message' => 'Launch ceremony state has been reset for re-testing!'
        ]);
    }

    /**
     * Interactive Pan-India Laboratory Network Map View
     */
    public function map()
    {
        $labs = [
            [
                'id' => 1,
                'name' => 'Textiles Committee HQ & Main Lab, Mumbai',
                'lat' => 19.0760,
                'lng' => 72.8777,
                'city' => 'Mumbai, Maharashtra',
                'type' => 'Central Excellence & Main Laboratory',
                'tests_monthly' => '18,500+',
                'speciality' => 'Eco-Testing, Fibre Analysis, Flame Retardancy',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 2,
                'name' => 'Port Inspection & Testing Lab, JNPT Mumbai',
                'lat' => 18.9500,
                'lng' => 72.9500,
                'city' => 'JNPT Navi Mumbai, Maharashtra',
                'type' => 'Port Export Consignment & Inspection Lab',
                'tests_monthly' => '14,200+',
                'speciality' => 'Port Fast-Track Certification, Container Inspection',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 3,
                'name' => 'Regional Laboratory, Ahmedabad',
                'lat' => 23.0225,
                'lng' => 72.5714,
                'city' => 'Ahmedabad, Gujarat',
                'type' => 'Denim & Woven Fabric Testing Centre',
                'tests_monthly' => '16,100+',
                'speciality' => 'Tear Strength, Abrasion Resistance, Dye Evaluation',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 4,
                'name' => 'Regional Laboratory, Bangalore',
                'lat' => 12.9716,
                'lng' => 77.5946,
                'city' => 'Bengaluru, Karnataka',
                'type' => 'Silk & Technical Textiles Lab',
                'tests_monthly' => '13,800+',
                'speciality' => 'Pure Silk Certification, Technical Textiles, PPE Testing',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 5,
                'name' => 'Regional Laboratory, Cannanore',
                'lat' => 11.8745,
                'lng' => 75.3704,
                'city' => 'Kannur (Cannanore), Kerala',
                'type' => 'Handloom & Traditional Textile Testing Centre',
                'tests_monthly' => '8,200+',
                'speciality' => 'Handloom Identification, GI Tag Authentication, Weave Density',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 6,
                'name' => 'Regional Laboratory, Chennai',
                'lat' => 13.0827,
                'lng' => 80.2707,
                'city' => 'Chennai, Tamil Nadu',
                'type' => 'Port Export Sample Testing Centre',
                'tests_monthly' => '12,500+',
                'speciality' => 'Consignment Testing, Port Certification, Fast-track QC',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 7,
                'name' => 'Regional Laboratory, Coimbatore',
                'lat' => 11.0168,
                'lng' => 76.9558,
                'city' => 'Coimbatore, Tamil Nadu',
                'type' => 'Yarn & Spinning Quality Lab',
                'tests_monthly' => '15,400+',
                'speciality' => 'Yarn Evenness, Tensile Strength, Moisture Content',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 8,
                'name' => 'Regional Laboratory, Guntur',
                'lat' => 16.3067,
                'lng' => 80.4365,
                'city' => 'Guntur, Andhra Pradesh',
                'type' => 'Cotton Quality & Fibre Testing Centre',
                'tests_monthly' => '11,200+',
                'speciality' => 'Ginned Cotton Quality, Fibre Length, Micronaire Value',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 9,
                'name' => 'Regional Laboratory, Hyderabad',
                'lat' => 17.3850,
                'lng' => 78.4867,
                'city' => 'Hyderabad, Telangana',
                'type' => 'Synthetic & Blended Textile Testing Lab',
                'tests_monthly' => '12,800+',
                'speciality' => 'Polyester Blends, Color Fastness, Chemical Testing',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 10,
                'name' => 'Regional Laboratory, Jaipur',
                'lat' => 26.9124,
                'lng' => 75.7873,
                'city' => 'Jaipur, Rajasthan',
                'type' => 'Hand Block Print & Craft Testing Centre',
                'tests_monthly' => '9,600+',
                'speciality' => 'Block Printing Fastness, Natural Dyes, Handicraft Authenticity',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 11,
                'name' => 'Regional Laboratory, Kanpur',
                'lat' => 26.4499,
                'lng' => 80.3319,
                'city' => 'Kanpur, Uttar Pradesh',
                'type' => 'Technical Textiles & Leather/Canvas Lab',
                'tests_monthly' => '10,400+',
                'speciality' => 'Heavy Industrial Canvas, Tensile & Bursting Strength',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 12,
                'name' => 'Regional Laboratory, Karur',
                'lat' => 10.9601,
                'lng' => 78.0766,
                'city' => 'Karur, Tamil Nadu',
                'type' => 'Home Textiles & Made-Ups Testing Centre',
                'tests_monthly' => '17,300+',
                'speciality' => 'Bed Linen, Terry Towels, Dimensional Stability, Shrinkage',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 13,
                'name' => 'Regional Laboratory, Kolkata',
                'lat' => 22.5726,
                'lng' => 88.3639,
                'city' => 'Kolkata, West Bengal',
                'type' => 'Jute & Natural Fibre Testing Centre',
                'tests_monthly' => '13,100+',
                'speciality' => 'Jute Quality Certification, Eco-Fibres, Tensile Strength',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 14,
                'name' => 'Regional Laboratory, Ludhiana',
                'lat' => 30.9010,
                'lng' => 75.8573,
                'city' => 'Ludhiana, Punjab',
                'type' => 'Woollen & Knitwear Testing Centre',
                'tests_monthly' => '14,800+',
                'speciality' => 'Thermal Resistance, Wool Blend Quality, Pilling Resistance',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 15,
                'name' => 'Regional Laboratory, New Delhi',
                'lat' => 28.6139,
                'lng' => 77.2090,
                'city' => 'New Delhi',
                'type' => 'Export Compliance & Regulatory Testing Lab',
                'tests_monthly' => '19,600+',
                'speciality' => 'Banned Azo Dyes, Heavy Metals, REACH Compliance',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 16,
                'name' => 'Regional Laboratory, Tirupur',
                'lat' => 11.1085,
                'lng' => 77.3411,
                'city' => 'Tirupur, Tamil Nadu',
                'type' => 'Knitwear & Garment Export Testing Centre',
                'tests_monthly' => '22,900+',
                'speciality' => 'Color Fastness, Organic Cotton, Shrinkage & Spirality',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 17,
                'name' => 'Regional Laboratory, Indore',
                'lat' => 22.7196,
                'lng' => 75.8577,
                'city' => 'Indore, Madhya Pradesh',
                'type' => 'Central India Textile Testing Centre',
                'tests_monthly' => '10,100+',
                'speciality' => 'Cotton Yarn Quality, Weave Inspection, Fabric Weight',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 18,
                'name' => 'Regional Laboratory, Ichalkaranji',
                'lat' => 16.6917,
                'lng' => 74.4604,
                'city' => 'Ichalkaranji, Maharashtra',
                'type' => 'Powerloom & Weaving Cluster Testing Lab',
                'tests_monthly' => '12,300+',
                'speciality' => 'Sizing Chemicals, Warp Tensile, Powerloom Fabric Quality',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
            [
                'id' => 19,
                'name' => 'Regional Laboratory, Bhubaneswar',
                'lat' => 20.2961,
                'lng' => 85.8245,
                'city' => 'Bhubaneswar, Odisha',
                'type' => 'Eastern Region Textile & Handloom Lab',
                'tests_monthly' => '8,900+',
                'speciality' => 'Sambalpuri Handloom Authentication, Natural Dye Analysis',
                'nabl' => 'ISO/IEC 17025 Certified'
            ],
        ];

        $metrics = [
            'total_labs' => count($labs),
            'monthly_samples' => '2,60,000+',
            'turnaround_days' => '3.2 Days',
            'tat_reduction' => '65%',
            'nabl_accredited_params' => '180+'
        ];

        return view('launch.map_demo', compact('labs', 'metrics'));
    }

    /**
     * 30-Second Interactive Sample Journey Walkthrough
     */
    public function simulator()
    {
        $steps = [
            [
                'step' => 1,
                'time_slot' => '00:00 - 00:06',
                'title' => 'Digital Sample Registration & QR Tagging',
                'subtitle' => 'Exporter or customer files online test request via LIMS portal',
                'icon' => 'ni-edit-doc',
                'badge' => 'Stage 1: Sample Onboarding',
                'role' => 'Exporter / Textile Mill Representative',
                'standards' => 'Govt. E-Governance & ISO/IEC 17025:2017 Guidelines',
                'input_output' => 'Input: Sample Specs & Test Selection ➔ Output: Tamper-Evident QR Code & Tracking ID',
                'details' => 'Exporters log into the portal to submit sample details, selecting physical, chemical, or eco-testing parameters (e.g., Fibre Blend, Tensile Strength, Banned Azo Dyes). The system instantly generates a unique cryptographic QR Code and tracking token for paperless sample dispatch.',
                'highlights' => [
                    'Instant cryptographic QR Code generation with embedded tracking metadata',
                    '100% paperless digital application – zero physical form filings required',
                    'Automated SMS & Email dispatch with real-time tracking link sent to exporter'
                ],
                'metrics' => [
                    ['label' => 'TAT Saved', 'val' => '4.5 Hours'],
                    ['label' => 'Paper Usage', 'val' => '0% Paperless'],
                    ['label' => 'Dispatch Speed', 'val' => 'Instant QR']
                ],
                'mock_type' => 'registration'
            ],
            [
                'step' => 2,
                'time_slot' => '00:06 - 00:12',
                'title' => 'Blind Coding & Cryptographic Allocation',
                'subtitle' => 'Zero-bias lab receipt with automated identity masking',
                'icon' => 'ni-shield-check',
                'badge' => 'Stage 2: Confidentiality Guarantee',
                'role' => 'Sample Receiving Officer & Automated Algorithm',
                'standards' => 'ISO/IEC 17025 Section 7.4 (Confidentiality & Impartiality)',
                'input_output' => 'Input: Physical Sample Package ➔ Output: Masked Cryptographic Barcode (#BC-88392-X92)',
                'details' => 'Upon sample arrival at the regional laboratory, the sample is scanned into intake. The system automatically masks company identity, replacing names with an encrypted 12-digit Blind Code Barcode. Analysts perform testing completely blind to prevent bias or external influence.',
                'highlights' => [
                    '100% Impartial & Unbiased testing – zero manufacturer identity exposed to lab analysts',
                    'Cryptographic 256-bit hashing links blind barcode to original application securely',
                    'Automated intelligent rack allocation tagging sample location in temperature-controlled store'
                ],
                'metrics' => [
                    ['label' => 'Impartiality Index', 'val' => '100% Blind'],
                    ['label' => 'Intake Processing', 'val' => '< 30 Seconds'],
                    ['label' => 'Security Level', 'val' => '256-Bit Encrypted']
                ],
                'mock_type' => 'blind_coding'
            ],
            [
                'step' => 3,
                'time_slot' => '00:12 - 00:18',
                'title' => 'Analyst Digital Workbench & Result Entry',
                'subtitle' => 'Structured digital test recording with automated tolerance calculations',
                'icon' => 'ni-flask',
                'badge' => 'Stage 3: Laboratory Analysis',
                'role' => 'Certified Chemical & Physical Lab Analyst',
                'standards' => 'ISO 1833, ISO 105, EN 14362-1, AATCC & BIS Standards',
                'input_output' => 'Input: Masked Blind Sample ➔ Output: Verified Digital Workbench Test Entry',
                'details' => 'Analyst performs physical and chemical testing on the blind sample using lab equipment. Test observations are entered directly into the structured LIMS workbench, which automatically calculates averages, standard deviations, and checks results against BIS/ISO pass thresholds.',
                'highlights' => [
                    'Structured digital result entry forms customized by test parameter and standard',
                    'Automated computation of mean values, tolerances, and automatic pass/fail evaluation',
                    'Immutable digital audit logs capturing analyst ID, test completion time, and raw observations'
                ],
                'metrics' => [
                    ['label' => 'Data Accuracy', 'val' => '99.9% Verified'],
                    ['label' => 'Calc Time', 'val' => 'Instant Auto'],
                    ['label' => 'Audit Log', 'val' => '100% Digital']
                ],
                'mock_type' => 'testing'
            ],
            [
                'step' => 4,
                'time_slot' => '00:18 - 00:24',
                'title' => 'Two-Tier Quality Review & Digital Sign-Off',
                'subtitle' => 'Senior Scientist validation & PKI digital signature',
                'icon' => 'ni-check-circle-cut',
                'badge' => 'Stage 4: Quality Assurance',
                'role' => 'Senior Quality Manager & Technical Authority',
                'standards' => 'NABL ISO/IEC 17025:2017 Technical Sign-Off Matrix',
                'input_output' => 'Input: Recorded Workbench Results ➔ Output: Digitally Signed QC Approval & e-Seal',
                'details' => 'Senior Quality Officer reviews recorded parameter values against national & international regulatory specs (BIS, ISO, ASTM, AATCC). Upon successful verification, the report is authorized using PKI-based Digital Signature (e-Sign), making it legally binding worldwide.',
                'highlights' => [
                    'Automated verification against national (BIS) & international (ISO/AATCC) benchmarks',
                    'PKI-based Digital Signature (e-Sign) guaranteeing legal validity & non-repudiation',
                    'Automated smart flagging of out-of-tolerance results requiring mandatory re-test'
                ],
                'metrics' => [
                    ['label' => 'Review Speed', 'val' => '< 2 Minutes'],
                    ['label' => 'Accreditation', 'val' => 'NABL ISO 17025'],
                    ['label' => 'Signature Type', 'val' => 'PKI e-Sign']
                ],
                'mock_type' => 'quality_review'
            ],
            [
                'step' => 5,
                'time_slot' => '00:24 - 00:30',
                'title' => 'QR-Authenticated PDF & Global Verification',
                'subtitle' => 'Instant downloadable certificate with anti-counterfeit QR code',
                'icon' => 'ni-file-docs',
                'badge' => 'Stage 5: Certificate Dispatch',
                'role' => 'Exporter, Port Customs & Overseas Buyers',
                'standards' => 'Global Anti-Counterfeiting & Public Verification Standard',
                'input_output' => 'Input: Approved Digital Test File ➔ Output: Verified PDF Report with Smart QR Code',
                'details' => 'The final NABL-accredited test certificate is compiled and dispatched automatically. Exporters receive instant download links via SMS/Email. Customs officials and international buyers can scan the embedded QR code to verify authenticity globally in 1 second.',
                'highlights' => [
                    'Instant PDF compilation with embedded high-resolution anti-counterfeit QR Code',
                    'Global 1-Second Verification Portal allowing instant public authenticity checks',
                    'Secure cloud storage with lifetime access and immutable document hash verification'
                ],
                'metrics' => [
                    ['label' => 'Report Delivery', 'val' => 'Instant Download'],
                    ['label' => 'QR Verification', 'val' => '1 Second Global'],
                    ['label' => 'Total Journey TAT', 'val' => '30s Walkthrough']
                ],
                'mock_type' => 'certificate'
            ]
        ];

        return view('launch.simulator', compact('steps'));
    }

    /**
     * Live QR Code Verification Stage Demo
     */
    public function qrDemo()
    {
        $sample = [
            'report_no' => 'TC/MUM/2026/08/9482',
            'certificate_id' => 'NABL-TC-2026-9482',
            'hash' => '0x8F9A34B219E04C7D',
            'sample_name' => '100% Organic Cotton Woven Fabric (Export Quality)',
            'applicant' => 'Bharat Textiles & Exports Pvt. Ltd.',
            'lab_location' => 'Textiles Committee Central Laboratory, Mumbai HQ',
            'date_tested' => '22 August 2026',
            'issued_at' => '22 August 2026, 11:30 AM',
            'status' => 'PASSED (NABL Certified)',
            'signatory' => 'Dr. R. K. Sharma',
            'signatory_title' => 'Senior Quality Officer & Technical Signatory (NABL ISO 17025)',
            'tests' => [
                ['name' => 'Fibre Composition', 'result' => '100% Organic Cotton', 'standard' => 'ISO 1833', 'status' => 'Pass'],
                ['name' => 'Banned Azo Dyes', 'result' => 'Not Detected (< 5 ppm)', 'standard' => 'EN 14362-1', 'status' => 'Pass'],
                ['name' => 'pH of Water Extract', 'result' => '6.8 (Neutral)', 'standard' => 'ISO 3071', 'status' => 'Pass'],
                ['name' => 'Tensile Strength (Warp/Weft)', 'result' => '450 N / 410 N', 'standard' => 'ISO 13934-1', 'status' => 'Pass'],
                ['name' => 'Color Fastness to Washing', 'result' => 'Grade 4-5 (Excellent)', 'standard' => 'ISO 105-C06', 'status' => 'Pass'],
            ]
        ];

        return view('launch.qr_demo', compact('sample'));
    }
}
