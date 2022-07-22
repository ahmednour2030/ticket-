<?php

namespace App\Models;

use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, UUIDTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'city',
        'address',
        'phone',
        'count',
        'price',
        'total_price',
        'ticket_id',
        'entry_at',
        'strip_id'
    ];
}
