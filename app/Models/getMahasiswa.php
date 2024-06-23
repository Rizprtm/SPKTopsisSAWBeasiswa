<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    public function getMahasiswa()
    {
        return $this->hasOne('App\getMahasiswa');
    }
}
