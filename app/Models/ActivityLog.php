<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'reference_id',
        'reference_type',
        'action_type',
        'description',
        'user_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function reference()
    {
        return $this->morphTo();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
