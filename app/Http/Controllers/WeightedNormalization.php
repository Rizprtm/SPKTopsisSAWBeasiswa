<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\TopsisService;

class WeightedNormalization extends Controller
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
        $weightedScores = $this->topsisService->weightNormalizedScores($normalizedScores, $criteriaweights);

        return view('weighted_normalization', compact('co_admin','weightedScores', 'criteriaweights'));
    }
    public function view()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
            ->first();
            $user = Auth::user();
            $userId = $user->userId;
            $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
            ->where('users.userId', $user->userId)
            ->select('admin_jurusan.nama')
            ->first();
        // $nama = $mahasiswa->nama;
        $periode = Periode::all();
        return view('weightednormalization_view', compact('co_admin','periode','mahasiswa','userId'));
    }
}
