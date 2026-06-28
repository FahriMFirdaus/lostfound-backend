<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'item_id', 'user_id', 'tanggal_klaim', 'bukti_teks', 'foto_bukti_pendukung', 
        'foto_ktp', 'status_verif', 'token_pengambilan', 'catatan_evaluasi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function handover()
    {
        return $this->hasOne(Handover::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'reference');
    }
}
