<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'priority',
        'posted_by',
        'posted_at',
        'is_active'
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by')->withDefault([
            'name' => 'System'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('posted_at', 'desc');
    }
}