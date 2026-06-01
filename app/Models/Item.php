<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul', 'deskripsi', 'deskripsi_rahasia', 'foto', 'post_type', 
        'status', 'visibility', 'tanggal_lapor', 'category_id', 'location_id', 'user_id'
    ];
    
    // Privacy Shield: Atribut yang disembunyikan dari respon JSON publik
    protected $hidden = [
        'deskripsi_rahasia'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
