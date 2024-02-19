<?php

namespace App\Models;

use App\Enum\NotamStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notam extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $keyType = 'string';

    protected $casts = [
        'structure' => 'array',
        'status'    => NotamStatus::class,
    ];

    protected $guarded = [];
}