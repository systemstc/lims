<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageTest extends Model
{
    protected $table = 'm20_package_tests';
    protected $primaryKey = 'm20_package_test_id';
    protected $fillable = [
        'm19_package_id',
        'm12_test_id',
        'm15_standard_id',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class, 'm15_standard_id', 'm15_standard_id');
    }
}
