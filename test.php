<?php
require 'a:/wamp64/www/lims/vendor/autoload.php';
$app = require_once 'a:/wamp64/www/lims/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$samples = App\Models\SampleRegistration::with(['customer', 'buyer', 'thirdParty', 'cha'])
    ->whereNotNull('tr04_report_to')
    ->take(5)
    ->get();

foreach($samples as $s) {
    echo 'Ref: ' . $s->tr04_reference_id . "\n";
    echo 'Report_To: ' . $s->tr04_report_to . "\n";
    echo 'Customer Name: ' . ($s->customer ? $s->customer->m07_name : 'NULL') . "\n";
    echo 'Buyer Name: ' . ($s->buyer ? $s->buyer->m07_name : 'NULL') . "\n";
    echo 'Third Party Name: ' . ($s->thirdParty ? $s->thirdParty->m07_name : 'NULL') . "\n";
    echo 'CHA Name: ' . ($s->cha ? $s->cha->m07_name : 'NULL') . "\n";
    echo "--------------------------\n";
}
