<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Message;

class StaffRating extends Model
{
    use HasFactory;

    protected $table = 'staff_ratings';

    protected $fillable = [
        'ticket_id',
        'message_id',
        'user_id',
        'admin_id',
        'rating',
        'comment'
    ];

    /**
     * Get the ticket that was rated.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the message that was rated.
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the user who created the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who was rated.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}