<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use DataTables;

class PromoController extends Controller
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
            $data = Promo::latest('updated_at')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('updated_at', function($row){
                        return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                    })
                    ->addColumn('value', function($row){
                        return rupiah($row->value);
                    })
                    ->addColumn('status', function($row){
                        if($row->is_active){
                            return "<span class='badge badge-success'>Aktif</span>";
                        }else{
                            return "<span class='badge badge-secondary'>Non Aktif</span>";
                        }
                    })
                    ->addColumn('action', function($row){
                        
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('promos.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li class="dropdown-item"><a href="'.route('promos.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        </ul>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action','updated_at','status'])
                    ->make(true);
        }

        return view('promo.index');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('promo.create');
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
            'promo_code' => 'required|unique:promos,code',
            'value' => 'required|numeric'
        ]);

        try {
            //code...
            Promo::create([
                "name" => $request->input('name'),
                "code" => $request->input('promo_code'),
                "value" => $request->input('value'),
                "description" => $request->input('description'),
            ]);

            
            toast('Data berhasil ditambah','success');
            
            return redirect()->route('promos.index');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th);

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
        $promo = Promo::find($id);
        return view('promo.edit',compact('promo'));
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
            'promo_code' => 'required|unique:promos,code,'.$id.',',
            'value' => 'required|numeric',
            "inputStatus" => 'required'
        ]);

        try {
            //code...
            $promo = Promo::find($id);
            $data = [
                "name" => $request->input('name'),
                "code" => $request->input('promo_code'), 
                "description" => $request->input('description'),
                "value" => $request->input('value'), 
                "is_active" => $request->input('inputStatus')=="on"?1:0
            ];

            $promo->update($data);

            toast('Data berhasil diubah','success');
            
            return redirect()->route('promos.index');

        } catch (\Throwable $th) {
            //throw $th;
            toast('Terjadi kesalahan','error');
            
            return redirect()->back()->withInput();
            
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
        // try {

        //     $delete = Promo::find($id);
        //     $delete->delete();

        //     toast('Data berhasil dihapus','success');

        //     return redirect()->back();

        // } catch (\Throwable $th) {
        //     dd($th->getMessage());
        //     //throw $th;
        // }
    }
}
