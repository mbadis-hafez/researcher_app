<?php

namespace App\Models;

use App\Artist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Artwork extends Model
{
    protected $fillable = [
        'owner_id',
        // other fillable fields...
    ];
    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->id;
        }

        parent::save();
    }

    public function owner()
    {
        return $this->belongsTo(Artist::class, 'owner_id','id');
    }
}
