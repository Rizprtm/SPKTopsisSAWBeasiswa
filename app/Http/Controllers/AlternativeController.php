<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Services\TopsisService;
use App\Models\User;
use App\Models\M_Mahasiswa;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;

class AlternativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $topsisService;

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first(); 
        $periode_id = $request->periode_id;

        // Get unique alternatives by userId
        $alternatives = Alternative::select(
            'alternatives.id as alternative_id',
            'alternatives.userId as userId',
            'mahasiswa.nama as mahasiswa_nama',
            'mahasiswa.jurusan as mahasiswa_jurusan',
            'mahasiswa.prodi as mahasiswa_prodi'
        )
        ->join('alternativescores', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->leftJoin('mahasiswa', 'alternatives.userId', '=', 'mahasiswa.userId')
        ->where('alternativescores.periode_id', $periode_id)
        ->distinct()
        ->get();

        // Get scores
        $scores = AlternativeScore::select(
            'alternativescores.id as id',
            'alternativescores.rating as rating',
            'alternativescores.status as status',
            'alternatives.id as alternative_id',
            'alternatives.userId as userId',
            'criteriaweights.id as criteria_id',
            'criteriaweights.name as criteria',
            'criteriaweights.weight as weight',
            'alternativescores.dokumen'
        )
        ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
        ->leftJoin('mahasiswa', 'alternatives.id', '=', 'mahasiswa.userId')
        ->where('alternativescores.periode_id', $periode_id)
        
        ->get();
        $criteriaweights = CriteriaWeight::all();

        return view('alternative.index', compact('criteriaweights','co_admin','alternatives', 'scores'))->with('i', 1);
    
    }
    public function view()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi', 'mahasiswa.userId')
            ->first();
        $periode = Periode::all();
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();
        return view('alternative.view', compact('co_admin','periode', 'mahasiswa', 'userId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
            
            $user = Auth::user();
            $userId = $user->userId;
            $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
                ->where('users.userId', $user->userId)
                ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
                ->first();
            $nama = $mahasiswa->nama;
            $periode_id = $request->periode_id;   
            // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
            // $data = M_Mahasiswa::all();
            
        // Cek apakah mahasiswa sudah terdaftar dalam periode ini
        $isRegistered = Alternative::where('userId', $userId)
            ->whereHas('alternativeScores', function($query) use ($periode_id) {
                $query->where('periode_id', $periode_id);
            })
            ->exists();

            $criteriaweights = CriteriaWeight::get();
            
            return view('alternative.create', compact('periode_id','criteriaweights','mahasiswa','userId','isRegistered'));
            


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // ddd($request);
            
            $periode_id = $request->periode_id;
            $user = Auth::user();
            $userId = $user->userId;
            // Save the alternative
            $alt = new Alternative;
            $alt->userId = $userId;
            $alt->save();
            
    
            // Save the score
            $criteriaweight = CriteriaWeight::get();
            foreach ($criteriaweight as $cw) {
                $score = new AlternativeScore();
                $score->alternative_id = $alt->id;
                $score->criteria_id = $cw->id;
                $score->rating = $request->input('criteria')[$cw->id];
                $score->periode_id = $periode_id;
                $score->status = 'Pending';
                if ($request->hasFile('dokumen')) {
                    $file = $request->file('dokumen');
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->storeAs('dokumen', $fileName); // Misalnya, menyimpan file dalam folder 'dokumen' di penyimpanan lokal
                    $score->dokumen = $filePath; // Simpan path file ke dalam kolom 'dokumen'
                }

                $score->save();
                
            }
            

            return redirect()->route('alternatives.create')
                ->with('success', 'Data disimpan permanen.');
            // If the operation was successful, send a success response
            return response()->json(['status' => 'success']);
        

            // If no duplicate, proceed with creating the user
            // Your code for creating the user goes here


        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak bisa diubah (silakan hubungi admin)');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function show(Alternative $alternative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function edit(Alternative $alternative)
    {
        $criteriaweights = CriteriaWeight::get();
     
        $alternativescores = AlternativeScore::where('alternative_id', $alternative->id)->get();
        return view('alternative.edit', compact('alternative', 'alternativescores', 'criteriaweights'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alternative $alternative)
    {
        // Save the score
        
        $scores = AlternativeScore::where('alternative_id', $alternative->id)->get();
        $criteriaweight = CriteriaWeight::get();
        foreach ($criteriaweight as $key => $cw) {
            $scores[$key]->rating = $request->input('criteria')[$cw->id];
            $scores[$key]->save();
        }

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alternative $alternative)
    {
        $scores = AlternativeScore::where('alternative_id', $alternative->id)->delete();
        $alternative->delete();

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|string|in:pending,terverifikasi,ditolak',
        ]);

        // Temukan AlternativeScore berdasarkan ID dan perbarui statusnya
        $score = AlternativeScore::find($id);
        if ($score) {
            $score->status = $request->input('status');
            $score->save();

            return redirect()->route('alternatives.index')->with('success', 'Status updated successfully');
        } else {
            return redirect()->route('alternatives.index')->with('error', 'AlternativeScore not found');
        }
    }
    
}
