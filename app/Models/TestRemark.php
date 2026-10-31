<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRemark extends Model
{
    use HasFactory;

    protected $table = 'm24_test_remarks';
    protected $primaryKey = 'm24_remark_id';

    protected $fillable = [
        'm04_ro_id',
        'm06_user_id',
        'm12_test_id',
        'm24_remark_text',
    ];

    /**
     * Regional Office relationship
     */
    public function ro()
    {
        return $this->belongsTo(RO::class, 'm04_ro_id', 'm04_ro_id');
    }

    /**
     * User / Employee who created the remark
     */
    public function user()
    {
        return $this->belongsTo(Employee::class, 'm06_user_id', 'm06_employee_id');
    }

    /**
     * Test relationship
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }
}

