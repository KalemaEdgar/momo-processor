<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'client_id', 'debit_account', 'credit_account', 'transaction_type', 'amount', 'status', 'reason', 'client_ip', 'created_by',
    ];
}
