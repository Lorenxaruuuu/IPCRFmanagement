<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ipcrf extends Model
{
    use HasFactory;

    protected $table = 'ipcrfs';

    protected $fillable = [
        'name',
        'province',
        'municipality',
        'evaluated_file_path',
        'scanned_file_path',
        'status'
    ];
}