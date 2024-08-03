<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alternatives';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId'
    ];
    public function alternativeScores()
    {
        return $this->hasMany(AlternativeScore::class, 'alternative_id', 'id');
    }
    public function mahasiswa()
    {
        return $this->belongsTo(M_Mahasiswa::class, 'userId', 'userId');
    }
}
