<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'customer_name',
        'customer_phone',
        'reservation_time',
        'guest_count',
        'user_id',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(ReservationChangeRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
