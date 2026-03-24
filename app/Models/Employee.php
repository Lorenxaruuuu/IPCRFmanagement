<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 
        'first_name', 
        'last_name', 
        'middle_name',
        'school_id', 
        'role', 
        'email'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function ipcrfRecords()
    {
        return $this->hasMany(IpcrfRecord::class);
    }

    public function fullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}