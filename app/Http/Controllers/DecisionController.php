<?php

namespace App\Http\Controllers;

use App\Services\TopsisService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Http\Request;
use App\Models\AlternativeScore;
use App\Models\Alternative;
use App\Models\CriteriaWeight;

class DecisionController extends Controller
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

        return view('decision', compact('co_admin','scores', 'criteriaweights','alternatives'))->with('i', 0);
        
    }

    public function view()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi', 'mahasiswa.userId')
            ->first();

         $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();
        $periode = Periode::all();

        return view('decisionview', compact('co_admin','periode', 'mahasiswa', 'userId'));
    }
}
