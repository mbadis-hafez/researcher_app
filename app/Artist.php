<?php

namespace App;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Model;


class Artist extends Model
{
    public function artworks()
{
    return $this->hasMany(Artwork::class, 'owner_id');
}
}
