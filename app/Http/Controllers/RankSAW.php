<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Services\SawService;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \PDF;

use Illuminate\Support\Facades\DB;

class RankSAW extends Controller
{
    protected $sawService;

    public function __construct(SawService $sawService)
    {
        $this->sawService = $sawService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
            ->where('users.userId', $user->userId)
            ->select('admin_jurusan.nama')
            ->first();
        $periode_id = $request->periode_id;
        $scores = $this->sawService->fetchScores($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $alternatives = $this->sawService->fetchAlternatives($periode_id);
        $normalizedScores = $this->sawService->normalizeScores($scores, $criteriaweights);
        $preferences = $this->sawService->preferences($normalizedScores, $criteriaweights);
    
        // Ambil data mahasiswa sekaligus dengan alternatif
        $alternatives = Alternative::with('mahasiswa')
            ->whereIn('id', $alternatives->pluck('id'))
            ->get();
        
        $alternativePreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferences[$alternative->id])) {
                $alternativePreferences[$alternative->id] = $preferences[$alternative->id];
            }
        }
    
        arsort($alternativePreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
        $quota = 100; // Tentukan jumlah alternatif yang diambil
        $selectedAlternatives = array_slice($alternativePreferences, 0, $quota, true);
    
        return view('saw/rank', compact('co_admin', 'scores', 'selectedAlternatives', 'alternatives', 'periode_id', 'preferences', 'criteriaweights'));
    }

    
    
    
    
    
    public function index2(Request $request)
    {
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
            ->where('users.userId', $user->userId)
            ->select('admin_jurusan.nama')
            ->first();
        $periode_id = $request->periode_id;
        $scores = $this->sawService->fetchScores($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $alternatives = $this->sawService->fetchAlternatives($periode_id);
        $normalizedScores = $this->sawService->normalizeScores($scores, $criteriaweights);
        $preferences = $this->sawService->preferences($normalizedScores, $criteriaweights);
    
        // Ambil data mahasiswa sekaligus dengan alternatif
        $alternatives = Alternative::with('mahasiswa')
            ->whereIn('id', $alternatives->pluck('id'))
            ->get();
        
        $alternativePreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferences[$alternative->id])) {
                $alternativePreferences[$alternative->id] = $preferences[$alternative->id];
            }
        }
    
        arsort($alternativePreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
        $quota = 100; // Tentukan jumlah alternatif yang diambil
        $selectedAlternatives = array_slice($alternativePreferences, 0, $quota, true);
    
        return view('saw/rank', compact('co_admin', 'scores', 'selectedAlternatives', 'alternatives', 'periode_id', 'preferences', 'criteriaweights'));
    }
    
    
    public function view()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
            ->first();
        // $nama = $mahasiswa->nama;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();
        $periode = Periode::all();
        return view('saw/rankview', compact('co_admin','periode','mahasiswa','userId'));
    }
    public function generatePDF(Request $request)
    {

        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
            ->where('users.userId', $user->userId)
            ->select('admin_jurusan.nama')
            ->first();
        $periode_id = $request->periode_id;
        $scores = $this->sawService->fetchScores($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $alternatives = $this->sawService->fetchAlternatives($periode_id);
        $normalizedScores = $this->sawService->normalizeScores($scores, $criteriaweights);
        $preferences = $this->sawService->preferences($normalizedScores, $criteriaweights);
    
        // Ambil data mahasiswa sekaligus dengan alternatif
        $alternatives = Alternative::with('mahasiswa')
            ->whereIn('id', $alternatives->pluck('id'))
            ->get();
        
        $alternativePreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferences[$alternative->id])) {
                $alternativePreferences[$alternative->id] = $preferences[$alternative->id];
            }
        }
    
        arsort($alternativePreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
        $quota = 100; // Tentukan jumlah alternatif yang diambil
        $selectedAlternatives = array_slice($alternativePreferences, 0, $quota, true);
        
            $data = [
                'co_admin' => $co_admin,
                'scores' => $scores,
                'selectedAlternatives' => $selectedAlternatives,
                'alternatives' => $alternatives,
                'periode_id' => $periode_id,
                'preferences' => $preferences,
                'criteriaweights' => $criteriaweights
            ];
        
            $pdf = PDF::loadView('rank_pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('rank_report.pdf');
    }
        

    public function fetchMahasiswaNama($userId)
{
    return Mahasiswa::where('userId', $userId)->value('nama');
}

public function fetchMahasiswaJurusan($userId)
{
    return Mahasiswa::where('userId', $userId)->value('jurusan');
}

public function fetchMahasiswaProdi($userId)
{
    return Mahasiswa::where('userId', $userId)->value('prodi');
}
}
