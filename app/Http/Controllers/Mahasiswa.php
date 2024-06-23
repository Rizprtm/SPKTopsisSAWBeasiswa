<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\CriteriaRating;
use App\Models\getMahasiswa;
use App\Models\M_Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Mahasiswa extends Controller
{

    
    public function profile()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId','mahasiswa.email','mahasiswa.hp')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        $data = M_Mahasiswa::all();
        return view('mahasiswa.profile',['data','mahasiswa'=> $mahasiswa]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formulir()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        $data = M_Mahasiswa::all();
        return view('mahasiswa.formulir',['data','mahasiswa'=> $mahasiswa]);
    }

    public function datamhs()
    {
        {
            $scores = AlternativeScore::select(
                'alternativescores.id as id',
                'alternatives.id as ida',
                'criteriaweights.id as idw',
                'criteriaratings.id as idr',
                'alternatives.userId as userId',
                'criteriaweights.name as criteria',
                'criteriaratings.rating as rating',
                'criteriaratings.description as description'
            )
                ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
                ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
                ->leftJoin('criteriaratings', 'criteriaratings.id', '=', 'alternativescores.rating_id')
                ->get();
    
            // duplicate scores object to get rating value later,
            // because any call to $scores object is pass by reference
            // clone, replica, tobase didnt work
            $cscores = AlternativeScore::select(
                'alternativescores.id as id',
                'alternatives.id as ida',
                'criteriaweights.id as idw',
                'criteriaratings.id as idr',
                'alternatives.userId as userId',
                'criteriaweights.name as criteria',
                'criteriaratings.rating as rating',
                'criteriaratings.description as description'
            )
                ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
                ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
                ->leftJoin('criteriaratings', 'criteriaratings.id', '=', 'alternativescores.rating_id')
                ->get();
    
    
    
            $alternatives = Alternative::get();
    
            $criteriaweights = CriteriaWeight::get();
    
            // Normalization
            foreach ($alternatives as $a) {
                // Get all scores for each alternative id
                $afilter = $scores->where('ida', $a->id)->values()->all();
                // Loop each criteria
                foreach ($criteriaweights as $icw => $cw) {
                    // Get all rating value for each criteria
                    $rates = $cscores->map(function ($val) use ($cw) {
                        if ($cw->id == $val->idw) {
                            return $val->rating;
                        }
                    })->toArray();
    
                    // array_filter for removing null value caused by map,
                    // array_values for reiindex the array
                    $rates = array_values(array_filter($rates));
    
                    if ($cw->type == 'benefit') {
                        $result = $afilter[$icw]->rating / max($rates);
                        $msg = 'rate ' . $afilter[$icw]->rating . ' max ' . max($rates) . ' res ' . $result;
                    } elseif ($cw->type == 'cost') {
                        $result = min($rates) / $afilter[$icw]->rating;
                    }
                    $result *= $cw->weight;
                    $afilter[$icw]->rating = round($result, 2);
                }
            }
    
            return view('rank', compact('scores', 'alternatives', 'criteriaweights'))->with('i', 0);
        }
        $user = Auth::user();
        $userId = $user->userId;
        
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();

        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama','mahasiswa.jurusan', 'mahasiswa.prodi')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        $data = M_Mahasiswa::all();
        return view('mahasiswa.datamhs',['data','mahasiswa','co_admin'=> $mahasiswa]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // Save the alternative
        $alt = new Alternative;
        $alt->name = $request->name;
        $alt->save();

        // Save the score
        $criteriaweight = CriteriaWeight::get();
        foreach ($criteriaweight as $cw) {
            $score = new AlternativeScore();
            $score->alternative_id = $alt->id;
            $score->criteria_id = $cw->id;
            $score->rating_id = $request->input('criteria')[$cw->id];
            $score->save();
        }

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative created successfully.');
    }
    public function create()
    {
        $criteriaweights = CriteriaWeight::get();
        $criteriaratings = CriteriaRating::get();
        return view('alternative.create', compact('criteriaweights', 'criteriaratings'));
    }
}
