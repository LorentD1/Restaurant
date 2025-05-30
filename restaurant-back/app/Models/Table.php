<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    // Fushat që mund të mbushen
    protected $fillable = [
        'name',
        'seats',
        'image', // Shtuar fusha për imazhin
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
