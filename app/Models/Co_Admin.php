<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Co_Admin extends Model
{
    use HasFactory;
    protected $table = "admin_jurusan";
    protected $fillable = [
        'id',
        'userId',
        'nama',
    ];      
}
