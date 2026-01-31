<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
    ];

    /**
     * Get the category that owns the ticket.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent that this ticket is assigned to.
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the comments for the ticket.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
