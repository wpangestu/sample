<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        if ($request->ajax()) {
            $data = Bank::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function($row){   
                        return $row->created_at->format('d/m/Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('updated_at', function($row){   
                        return $row->updated_at->format('d/m/Y')."<br>".$row->updated_at->format('H:i:s');
                    })
                    ->addColumn('logo', function($row){   
                        return '<img src="'.$row->logo.'" class="img-fluid">';
                    })
                    ->addColumn('action', function($row){   
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('banks.edit',$row->id).'" data-toggle="tooltip" data-original-title="Ubah"><i class="fa fa-edit"></i> Ubah</a></li>
                            <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('banks.destroy',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times"></i> Hapus</a></li>
                        </ul>
                        ';
                        return $btn;
                            // $btn = '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';    
                            // $btn .= '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';
                    })
                    ->rawColumns(['action','logo','created_at','updated_at'])
                    ->make(true);
        }

        return view('bank.index');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('bank.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            //code...
            $data = [
                "name" => $request->input('name'),
                "account_number" => $request->input('account_number'),
                // 'image' => $request->input('price'),
            ];

            $insert = Bank::create($data);

            if ($request->hasFile('logo')) {

                $uploadFolder = 'bank/image';
                $photo = $request->file('logo');
                $photo_path = $photo->store($uploadFolder, 'public');
    
                $insert->logo = Storage::disk('public')->url($photo_path);
                $insert->save();
            }

            $causer = auth()->user();

            activity('add_bank')->performedOn($insert)
                        ->causedBy($causer)
                        ->log('Data berhasil ditambahkan');

            toast('Data berhasil ditambah','success');
                        
            return redirect()->route('banks.index');

        } catch (\Throwable $th) {
            //throw $th;
            // dd($th->getMessage());
            toast('Maaf terjadi kesalahan','error');
                        
            return redirect()->route('bank.create');
        }        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = Bank::find($id);
        return view('bank.edit',compact('data'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            "name" => $request->input('name'),
            "account_number" => $request->input('account_number'),
        ];

        try {
            //code...
            $bank = Bank::find($id);
            $bank->update($data);

            if ($request->hasFile('logo')) {

                $uploadFolder = 'bank/image';
                $photo = $request->file('logo');
                $photo_path = $photo->store($uploadFolder, 'public');
    
                $bank->logo = Storage::disk('public')->url($photo_path);
                $bank->save();
            }

            $causer = auth()->user();

            activity('edit_bank')->performedOn($bank)
                        ->causedBy($causer)
                        ->log('Data berhasil diubah');

            toast('Data berhasil diubah','success');
                        
            return redirect()->route('banks.index');
            
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {

            $delete = Bank::find($id);
            $delete->delete();

            $causer = auth()->user();

            activity('delete_bank')->performedOn($delete)
                        ->causedBy($causer)
                        ->log('Data berhasil dihapus');

            toast('Data berhasil dihapus','success');

            return redirect()->back();

        } catch (\Throwable $th) {
            // dd($th->getMessage());
            toast('Maaf terjadi kesalahan','error');

            return redirect()->back();
            //throw $th;
        }

    }
}
