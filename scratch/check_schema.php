<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = Schema::getColumnListing('m19_packages');
echo "Columns in m19_packages:\n";
print_r($columns);

$columns2 = Schema::getColumnListing('m20_package_tests');
echo "\nColumns in m20_package_tests:\n";
print_r($columns2);
