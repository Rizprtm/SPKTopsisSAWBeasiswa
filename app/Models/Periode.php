<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

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
    

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($periode) {
            Log::info('Deleting periode: ' . $periode->id);

            // Hapus alternativescores yang terkait
            $periode->deleteAlternativeScores();

            // Hapus alternatives yang terkait
            $periode->deleteAlternatives();
        });
    }

    // Fungsi untuk menghapus alternative scores yang terkait
    public function deleteAlternativeScores()
    {
        // Ambil alternative_id yang terkait dengan periode ini
        $alternativeIds = AlternativeScore::where('periode_id', $this->periode_id)->pluck('alternative_id')->toArray();

        Log::info('Alternative IDs to delete alternative scores: ' . implode(', ', $alternativeIds));

        // Hapus alternative scores yang terkait dengan periode ini
        AlternativeScore::whereIn('alternative_id', $alternativeIds)->where('periode_id', $this->periode_id)->delete();

        Log::info('Alternative scores deleted for alternative IDs: ' . implode(', ', $alternativeIds));

        return $alternativeIds;
    }

    // Fungsi untuk menghapus alternatives yang terkait
    public function deleteAlternatives()
    {
        // Ambil alternative_id yang terkait dengan periode ini
        $alternativeIds = AlternativeScore::where('periode_id', $this->periode_id)->pluck('alternative_id')->toArray();

        Log::info('Alternative IDs to delete: ' . implode(', ', $alternativeIds));

        // Hapus alternatives yang terkait
        Alternative::whereIn('id', $alternativeIds)->delete();

        Log::info('Alternatives deleted for IDs: ' . implode(', ', $alternativeIds));
    }

    // Fungsi untuk menghapus alternatives yang terkait

}



