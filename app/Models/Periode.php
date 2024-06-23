<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;
    protected $table = "periode";
    protected $primaryKey = 'periode_id';
    protected $fillable = [
        'periode_id',
        'tanggal_buka',
        'tanggal_tutup',
        'periode_status',
        'periode_file',
    ];  
}
