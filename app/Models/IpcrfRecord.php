<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpcrfRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'semester',
        'school_year',
        'status',
        'remarks',
        'uploaded_at',
        'role'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}