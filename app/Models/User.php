<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'tr01_users';
    protected $primaryKey = 'tr01_user_id';
    protected $fillable = [
        'tr01_email',
        'tr01_name',
        'tr01_password',
        'tr01_type',
        'tr01_two_factor_method',
        'tr01_two_factor_secret',
        'tr01_two_factor_recovery_codes',
        'tr01_two_factor_confirmed_at',
        'tr01_is_2fa_blocked'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tr01_user_roles', 'tr01_user_id', 'm03_role_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'tr01_user_id', 'tr01_user_id');
    }

    public function ro()
    {
        return $this->hasOne(Ro::class, 'tr01_user_id', 'tr01_user_id');
    }

    /**
     * Retrieve the phone number from associated employee or RO record.
     */
    public function getPhoneNumber(): ?string
    {
        if ($this->relationLoaded('employee') && $this->employee && !empty($this->employee->m06_phone)) {
            return $this->employee->m06_phone;
        }
        if ($this->relationLoaded('ro') && $this->ro && !empty($this->ro->m04_phone)) {
            return $this->ro->m04_phone;
        }

        $emp = Employee::where('tr01_user_id', $this->tr01_user_id)->first();
        if ($emp && !empty($emp->m06_phone)) {
            return $emp->m06_phone;
        }

        $ro = Ro::where('tr01_user_id', $this->tr01_user_id)->first();
        if ($ro && !empty($ro->m04_phone)) {
            return $ro->m04_phone;
        }

        return null;
    }

    /**
     * Return masked phone number (e.g., +91 ******1234)
     */
    public function getMaskedPhoneNumber(): string
    {
        $phone = $this->getPhoneNumber();
        if (!$phone) {
            return 'Not Configured';
        }
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) >= 10) {
            $last4 = substr($digits, -4);
            return '+91 ******' . $last4;
        }
        return '+91 ' . substr($digits, 0, 2) . '****' . substr($digits, -2);
    }
}
