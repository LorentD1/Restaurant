<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_id',
        'type',
        'new_data',
        'status',
        'approved_by'
    ];

    protected $casts = [
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    
}
