<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\Periode;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NormalizationController extends Controller
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
        $criteriaweights = CriteriaWeight::all();
        $normalizedScores = $this->topsisService->normalizeScores($scores, $criteriaweights);

        return view('normalization', compact('co_admin','normalizedScores', 'criteriaweights'));
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
        return view('normalizationview', compact('co_admin','periode','mahasiswa','userId'));
    }
}
