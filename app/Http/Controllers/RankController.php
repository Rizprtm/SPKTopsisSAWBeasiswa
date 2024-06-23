<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Services\TopsisService;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\User;
use App\Models\M_Mahasiswa;
use Illuminate\Support\Facades\Auth;
use \PDF;

use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    protected $topsisService;

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
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
        $scores = $this->topsisService->fetchScores($periode_id);
        $alternatives = $this->topsisService->fetchAlternatives($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $normalizedScores = $this->topsisService->normalizeScores($scores, $criteriaweights);
        $weightedScores = $this->topsisService->weightNormalizedScores($normalizedScores, $criteriaweights);
        $idealSolutions = $this->topsisService->idealSolutions($weightedScores, $criteriaweights);
        $distances = $this->topsisService->calculateDistances($weightedScores, $idealSolutions);
        $preferenceValues = $this->topsisService->calculatePreferenceValues($distances);
        $rankings = $this->topsisService->calculateRanking($preferenceValues);
            // Ambil data mahasiswa sekaligus dengan alternatif
            $alternatives = Alternative::with('mahasiswa')
                ->whereIn('id', $alternatives->pluck('id'))
                ->get();
                $groupedAlternatives = $alternatives->groupBy('mahasiswa.prodi');
                $quota = [
                    'D-IV Teknologi Rekayasa Perangkat Lunak' => 2,
                    'D-III Teknik Sipil' => 3,
                    'D-IV Manajemen Perpajakan' => 1,
                    // Tentukan kuota untuk setiap prodi
                ];
                $selectedAlternatives = [];
            
                foreach ($groupedAlternatives as $prodi => $group) {
                    $groupPreferences = [];
                    foreach ($group as $alternative) {
                        if (isset($preferenceValues[$alternative->id])) {
                            $groupPreferences[$alternative->id] = $preferenceValues[$alternative->id];
                        }
                    }
            
                    arsort($groupPreferences); // Urutkan berdasarkan nilai preferensi tertinggi
            
                    $selectedAlternatives[$prodi] = array_slice($groupPreferences, 0, $quota[$prodi] ?? count($groupPreferences), true);
                    // dd($alternatives);
                }
        return view('rank', compact('co_admin','scores','selectedAlternatives','alternatives','periode_id','preferenceValues', 'criteriaweights'));
    
    }
    public function view()
    {


        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
            ->first();

        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first(); 
        // $nama = $mahasiswa->nama;
        $periode = Periode::all();
        return view('rankview', compact('co_admin','periode','mahasiswa','userId'));
    }
    public function generatePDF(Request $request)
    {
        $periode_id = $request->periode_id;
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
            ->where('users.userId', $user->userId)
            ->select('admin_jurusan.nama')
            ->first(); 
    
        $scores = $this->topsisService->fetchScores($periode_id);
        $alternatives = $this->topsisService->fetchAlternatives($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $normalizedScores = $this->topsisService->normalizeScores($scores, $criteriaweights);
        $weightedScores = $this->topsisService->weightNormalizedScores($normalizedScores, $criteriaweights);
        $idealSolutions = $this->topsisService->idealSolutions($weightedScores, $criteriaweights);
        $distances = $this->topsisService->calculateDistances($weightedScores, $idealSolutions);
        $preferenceValues = $this->topsisService->calculatePreferenceValues($distances);
        $rankings = $this->topsisService->calculateRanking($preferenceValues);
    
        // Ambil data mahasiswa sekaligus dengan alternatif
        $alternatives = Alternative::with('mahasiswa')
            ->whereIn('id', $alternatives->pluck('id'))
            ->get();
        $groupedAlternatives = $alternatives->groupBy('mahasiswa.prodi');
        $quota = [
            'D-IV Teknologi Rekayasa Perangkat Lunak' => 2,
            'D-III Teknik Sipil' => 3,
            'D-IV Manajemen Perpajakan' => 1,
            // Tentukan kuota untuk setiap prodi
        ];
        $selectedAlternatives = [];
    
        foreach ($groupedAlternatives as $prodi => $group) {
            $groupPreferences = [];
            foreach ($group as $alternative) {
                if (isset($preferenceValues[$alternative->id])) {
                    $groupPreferences[$alternative->id] = $preferenceValues[$alternative->id];
                }
            }
    
            arsort($groupPreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
            $selectedAlternatives[$prodi] = array_slice($groupPreferences, 0, $quota[$prodi] ?? count($groupPreferences), true);
        }
    
        $data = [
            'periode_id' => $periode_id,
            'co_admin' => $co_admin,
            'scores' => $scores,
            'selectedAlternatives' => $selectedAlternatives,
            'alternatives' => $alternatives,
            'preferenceValues' => $preferenceValues,
            'criteriaweights' => $criteriaweights,
        ];
    
        $pdf = PDF::loadView('rank_pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('rank_report.pdf');
    }
    
    
}
