<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Book extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function path()
    {
        return '/books/' . $this->id;
    }

    public function checkout(User $user)
    {
        $this->reservations()->create([
           'user_id' => $user->id,
           'checked_out_at' => now(),
        ]);
    }

    public function setAuthorIdAttribute($author)
    {
        $this->attributes['author_id'] = (Author::firstOrCreate([
            'name' => $author,
        ]))->id;
    }

    public function reservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
