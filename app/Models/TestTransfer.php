<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTransfer extends Model
{
    protected $table = 'tr06_test_transfers';
    protected $primaryKey = 'tr06_test_transfer_id';
    protected $fillable = [
        'tr05_sample_test_id',
        'm04_from_ro_id',
        'm04_to_ro_id',
        'm06_transferred_by',
        'm06_received_by',
        'tr06_transferred_at',
        'tr06_received_at',
        'tr06_reason',
        'tr06_remark',
    ];

    public function sampleTest()
    {
        return $this->belongsTo(SampleTest::class, 'tr05_sample_test_id', 'tr05_sample_test_id');
    }
}
