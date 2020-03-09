<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Telepon;
use App\Kelas;
use App\Hobi;
use App\Http\Requests\SiswaRequest;
use Storage;
use Session;

class SiswaController extends Controller
{
	public function __construct(){
		$this->middleware('auth', ['except' => [
			'index',
			'show',
			'cari',
		]]);
	}

    public function index(){
		$siswa_list = Siswa::orderBy('nama_siswa', 'asc')->paginate(3);
		$jumlah_siswa = Siswa::count();
		return view('siswa.index', compact('siswa_list', 'jumlah_siswa'));
    }

    public function create(){
    	return view('siswa.create');
    }

    public function store(SiswaRequest $request){
    	$input = $request->all();

    	//Upload foto
    	if($request->hasFile('foto')){
    		$input['foto'] = $this->uploadFoto($request);
    	}
    	    	
    	//simpan data siswa
    	$siswa = Siswa::create($input);

    	//Insert telepon
    	if($request->filled('nomor_telepon')){
    		$this->insertTelepon($siswa, $request);
    	}
    	
    	$siswa->hobi()->attach($request->input('hobi_siswa'));

    	Session::flash('flash_message', 'Data siswa berhasil disimpan.');

    	return redirect('siswa');
    }

    public function show(Siswa $siswa){
    	return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa){
    	if(!empty($siswa->telepon->nomor_telepon)){
    		$siswa->nomor_telepon = $siswa->telepon->nomor_telepon;
    	}

    	return view('siswa.edit', compact('siswa'));
    }

    public function update(Siswa $siswa, SiswaRequest $request){
    	$input = $request->all();

    	//Update foto
    	if($request->hasFile('foto')){
    		$input['foto'] = $this->updateFoto($siswa, $request);
    	}
    	
    	//Update data siswa
    	$siswa->update($input);

    	//Update telepon
    	$this->updateTelepon($siswa, $request);    	

    	//Update data hobi
    	$siswa->hobi()->sync($request->input('hobi_siswa'));

    	Session::flash('flash_message', 'Data siswa berhasil di update.');

    	return redirect('siswa');
    }

    public function destroy(Siswa $siswa){
    	//Hapus foto kalau ada
    	$this->hapusFoto($siswa);
    	
    	$siswa->delete();

    	Session::flash('flash_message', 'Data siswa berhasil di hapus.');
    	Session::flash('penting', true);

    	return redirect('siswa');
    }

    public function tesCollection(){
    	$collection = Siswa::all();
    	$jumlah = $collection->count();
    	return 'Jumlah data: '.$jumlah;
    }

    public function dateMutator(){
    	$siswa = Siswa::findOrFail(1);
    	return "Umur {$siswa->nama_siswa} adalah {$siswa->tanggal_lahir->age} tahun.";
    }

    private function insertTelepon(Siswa $siswa, SiswaRequest $request){
    	$telepon = new Telepon;
	    $telepon->nomor_telepon = $request->input('nomor_telepon');
	    $siswa->telepon()->save($telepon);
    }

    private function updateTelepon(Siswa $siswa, SiswaRequest $request){
    	//Update nomor telepon, jika sebelumnya sudah ada nomor telepon.
    	if($siswa->telepon){
    		//jika telp diisi update
    		if($request->filled('nomor_telepon')){
    			$telepon = $siswa->telepon;
    			$telepon->nomor_telepon = $request->input('nomor_telepon');
    			$siswa->telepon()->save($telepon);
    		}
    		//jika telp tidak diisi hapus
    		else{
    			$siswa->telepon()->delete();
    		}
    	}
    	//buat entry baru, jika sebelumnya tidak ada nomor telepon
    	else{
    		if($request->filled('nomor_telepon')){
    			$telepon = new Telepon;
    			$telepon->nomor_telepon = $request->input('nomor_telepon');
    			$siswa->telepon()->save($telepon);
    		}
    	}
    }

    private function uploadFoto(SiswaRequest $request){
    	$foto = $request->file('foto');
    	$ext = $foto->getClientOriginalExtension();
    	if($request->file('foto')->isValid()){
    		$foto_name = date('YmdHis'). ".$ext";
    		$upload_path = 'fotoupload';
    		$request->file('foto')->move($upload_path, $foto_name);
    		return $foto_name; 
    	}
    	return false;
    }

    private function updateFoto(Siswa $siswa, SiswaRequest $request){
    	//Foto. cek apakah ada foto?
    	if($request->hasFile('foto')){
    		//hapus foto lama jika ada foto baru
    		$exist = Storage::disk('foto')->exists($siswa->foto);
    		if(isset($siswa->foto) && $exist){
    			$delete = Storage::disk('foto')->delete($siswa->foto);
    		}

    		//Upload foto baru
    		$foto = $request->file('foto');
    		$ext = $foto->getClientOriginalExtension();
    		if($request->file('foto')->isValid()){
    			$foto_name = date('YmdHis'). ".$ext";
    			$upload_path = 'fotoupload';
    			$request->file('foto')->move($upload_path, $foto_name);
    			return $foto_name; 
    		}
    	}

    }

    private function hapusFoto(Siswa $siswa){
    	$is_foto_exist = Storage::disk('foto')->exists($siswa->foto);

    	if($is_foto_exist){
    		Storage::disk('foto')->delete($siswa->foto);
    	}
    }

    public function cari(Request $request){
    	$kata_kunci = trim($request->input('kata_kunci'));

    	if(!empty($kata_kunci)){
    		$jenis_kelamin = $request->input('jenis_kelamin');
    		$id_kelas = $request->input('id_kelas');

    		//Query    		
    		$query = Siswa::where('nama_siswa', 'LIKE', '%' . $kata_kunci . '%');
    		(!empty($jenis_kelamin)) ? $query->JenisKelamin($jenis_kelamin) : '';
    		(!empty($id_kelas)) ? $query->Kelas($id_kelas) : '';
    		$siswa_list = $query->paginate(2);

    		//URL links pagination
    		$pagination = (!empty($jenis_kelamin)) ? $siswa_list->appends(['jenis_kelamin' => $jenis_kelamin]) : '';
    		$pagination = (!empty($id_kelas)) ? $pagination = $siswa_list->appends(['id_kelas' => $id_kelas]) : '';
    		$pagination = $siswa_list->appends(['kata_kunci' => $kata_kunci]);

    		$jumlah_siswa = $siswa_list->total();    		
    		return view('siswa.index', compact('siswa_list', 'kata_kunci', 'pagination', 'jumlah_siswa', 'id_kelas', 'jenis_kelamin'));
    	}
    	return redirect('siswa');
    }
}
