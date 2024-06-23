<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\M_Mahasiswa;
use App\Models\AlternativeScore;
use App\Models\Periode;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function login()
    {
        return view('login');
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();  
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama')
            ->first();

  
        $totalMahasiswa = M_Mahasiswa::count();
        $formulir = AlternativeScore::count();
        $totalformulir = ceil($formulir / 3);
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        // $data = M_Mahasiswa::all();
        return view('dashboard',['data','mahasiswa'=> $mahasiswa, 'totalMahasiswa'=> $totalMahasiswa, 'totalformulir' => $totalformulir, 'co_admin' => $co_admin]);
    }
    public function periode()
    {
                // Lakukan pengecekan apakah terdapat entri yang menghubungkan mahasiswa dengan periode ini
                
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first(); 

        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId','mahasiswa.email','mahasiswa.hp')
            ->first();

        // $nama = $mahasiswa->nama;
        $dataperiode = Periode::all();
        $periodeData = $dataperiode->map(function ($periode) {
            $periode->jumlah_mahasiswa = AlternativeScore::where('periode_id', $periode->periode_id)
                 // Assuming alternative_id represents the unique student
                 ->distinct()
                ->count('alternative_id');
            return $periode;
        });
        return view('periode', compact('co_admin','periodeData','dataperiode','mahasiswa','userId'));
    }
    public function store_periode(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;


        // dd($request->all());
        $request->validate([
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'periode_status' => 'boolean',
            'periode_file' => 'required|file|mimes:pdf|max:2048', // Contoh validasi untuk file PDF maksimal 2MB
        ]);

        // Simpan data periode ke dalam database
        $periode = new Periode();
        $periode->tanggal_buka = $request->tanggal_buka;
        $periode->tanggal_tutup = $request->tanggal_tutup;
        $periode->periode_status = $request->periode_status;

        // Upload file periode
        if ($request->hasFile('periode_file')) {
            $file = $request->file('periode_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('periode_files'), $fileName);
            $periode->periode_file = $fileName;
        }

        $periode->save();

        return redirect()->route('periode_beasiswa.tambah')->with('success', 'Data periode berhasil ditambahkan.');
    
    }
    public function view_periode()
    {
        $user = Auth::user();
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();  
        return view('tambah_periode', compact('co_admin'));
    }
    public function viewPdf($periode_file)
    {
        $dataperiode = Periode::where('periode_file', $periode_file)->first();

        // Pastikan file PDF ditemukan
        if ($dataperiode && $dataperiode->periode_file) {
            // Konstruksi path lengkap ke file PDF
            $pdfFilePath = public_path('periode_files/' . $dataperiode->periode_file);
    
            // Pastikan file PDF ada
            if (File::exists($pdfFilePath)) {
                // Baca konten file PDF
                $pdfContent = File::get($pdfFilePath);
                
                // Kembalikan respons dengan tipe konten application/pdf
                return response($pdfContent)
                    ->header('Content-Type', 'application/pdf');
            }
        }
    
        // File PDF tidak ditemukan, kembalikan respons JSON dengan pesan kesalahan
        return response()->json(['error' => 'File PDF tidak ditemukan.'], 404);
    }
        public function periode_edit($id)
    {
        $user = Auth::user();
        $userId = $user->userId;
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();  
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
            ->first();
        $dataperiode = Periode::findOrFail($id);
        // Lakukan operasi lain yang diperlukan, seperti menampilkan form edit
        
        return view('edit_periode', compact('co_admin','dataperiode','mahasiswa','userId'));
    }

    public function periode_update(Request $request, $id)
    {
        $request->validate([
            // Atur validasi sesuai kebutuhan Anda untuk data yang akan diupdate
        ]);

        $dataperiode = Periode::findOrFail($id);
        // Update data periode dengan data baru dari request
        $dataperiode->update($request->all());

        return redirect()->route('periode_beasiswa')->with('success', 'Data periode berhasil diupdate.');
    }

    public function periode_destroy($periode_id)
    {
        $dataperiode = Periode::findOrFail($periode_id);
        // Hapus periode dari database
        $dataperiode->delete();

        return redirect()->route('periode_beasiswa')->with('success', 'Data periode berhasil dihapus.');
    }
}
