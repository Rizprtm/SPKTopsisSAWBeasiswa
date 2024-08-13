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
    
        // Ambil data mahasiswa sekaligus dengan alternatif
        $alternatives = Alternative::with('mahasiswa')
            ->whereIn('id', $alternatives->pluck('id'))
            ->get();
    
        // Urutkan semua alternatif berdasarkan preferensi secara keseluruhan
        $allPreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferenceValues[$alternative->id])) {
                $allPreferences[$alternative->id] = $preferenceValues[$alternative->id];
            }
        }
    
        arsort($allPreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
        // Tentukan kuota total untuk semua prodi
        $quota = 100; // Total jumlah alternatif yang diambil
    
        $selectedAlternatives = [];
        $count = 0;
    
        foreach ($allPreferences as $alternative_id => $preference) {
            if ($count >= $quota) break;
    
            $alternative = $alternatives->firstWhere('id', $alternative_id);
            if ($alternative) {
                $selectedAlternatives[] = [
                    'alternative' => $alternative,
                    'preference' => $preference
                ];
                $count++;
            }
        }
    
        return view('rank', compact('co_admin', 'scores', 'selectedAlternatives', 'alternatives', 'periode_id', 'criteriaweights'));
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
    
        // Urutkan semua alternatif berdasarkan preferensi secara keseluruhan
        $allPreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferenceValues[$alternative->id])) {
                $allPreferences[$alternative->id] = $preferenceValues[$alternative->id];
            }
        }
    
        arsort($allPreferences); // Urutkan berdasarkan nilai preferensi tertinggi
    
        // Mengelompokkan alternatif berdasarkan prodi
        $groupedAlternatives = $alternatives->groupBy('mahasiswa.prodi');
    
        $quota = [
            'D-IV Teknologi Rekayasa Perangkat Lunak' => 2,
            'D-III Teknik Sipil' => 3,
            'D-IV Manajemen Perpajakan' => 1,
            // Tentukan kuota untuk setiap prodi
        ];
    
        $selectedAlternatives = [];
    
        // Pilih alternatif sesuai dengan kuota per prodi dari preferensi yang telah diurutkan
        $countPerProdi = [];
        foreach ($allPreferences as $alternative_id => $preference) {
            $alternative = $alternatives->firstWhere('id', $alternative_id);
            if ($alternative) {
                $prodi = $alternative->mahasiswa->prodi;
                if (!isset($countPerProdi[$prodi])) {
                    $countPerProdi[$prodi] = 0;
                }
                if ($countPerProdi[$prodi] < ($quota[$prodi] ?? 0)) {
                    $selectedAlternatives[] = [
                        'alternative' => $alternative,
                        'preference' => $preference
                    ];
                    $countPerProdi[$prodi]++;
                }
            }
        }
    
        return view('rank', compact('co_admin', 'scores', 'selectedAlternatives', 'alternatives', 'periode_id', 'preferenceValues', 'criteriaweights'));
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
    
        // Gabungkan alternatif dengan nilai preferensi
        $alternativePreferences = [];
        foreach ($alternatives as $alternative) {
            if (isset($preferenceValues[$alternative->id])) {
                $alternativePreferences[$alternative->id] = $preferenceValues[$alternative->id];
            }
        }
    
        // Urutkan alternatif berdasarkan nilai preferensi tertinggi
        arsort($alternativePreferences);
    
        // Pilih alternatif terbaik, misalnya ambil top 10
        $selectedAlternatives = array_slice($alternativePreferences, 0, 20, true);
    
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
