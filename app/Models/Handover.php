<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    protected $fillable = [
        'claim_id', 'admin_id', 'foto_materai', 'foto_serah_terima', 'tanggal_serah_terima'
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
