<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Get the tickets for the category.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
