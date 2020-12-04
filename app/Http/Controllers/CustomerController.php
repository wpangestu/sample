<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Illuminate\Support\Str;

class CustomerController extends Controller
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
            $data = User::latest()->Role('user')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                            $btn = '<a href="'.route('customer.edit',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
   
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('customer.destroy',$row->userid).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('customer.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('customer.create');
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
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required'
        ]);

        $data = [
            "name"      => $request->input('name'),
            "email"     => $request->input('email'),
            "phone"     => $request->input('phone'),
            "password"  => $request->input('password'),
            "status"    => $request->input('active')??0,
            "address"   => $request->input('address'),
            "userid"    => Str::random(6),
        ];

        $insert = User::create($data);

        $insert->assignRole('user');

        if($insert){
            return redirect()->route('customer.index')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('customer.index')
                        ->with('error','Opps, Terjadi kesalahan.');
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
    }
}
