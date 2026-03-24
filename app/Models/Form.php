<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'download_count',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
     use HasFactory;

    protected $fillable2 = [
        'title',
        'category',
        'description',
        'file_path',
        'uploaded_by',
        'published_at',
        'is_active'
    ];

    protected $casts2 = [
        'published_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}