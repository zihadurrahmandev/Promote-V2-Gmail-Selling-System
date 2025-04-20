<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gmail extends Model
{
    use HasFactory;

    protected $table = 'gmail_accounts';

    protected $fillable = [
        'gmail',
        'password',
        're_gmail',
        'backup',
        'fb_2f_key',
        'status',
        'price',
        'user_id',
    ];


}
