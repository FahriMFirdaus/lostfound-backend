<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nama_tempat', 'detail_lokasi'];
    
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
