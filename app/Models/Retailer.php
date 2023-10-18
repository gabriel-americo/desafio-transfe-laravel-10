<?php

namespace App\Models;

use App\Models\Transactions\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Retailer extends Authenticatable
{
    use HasApiTokens, HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'document_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }
}
