<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Illuminate\Support\Str;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Models\UserBankAccount;
use Maatwebsite\Excel\Facades\Excel;

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
            $data = User::latest()->Role('user')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('updated_at', function($row){
                        return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                    })
                    ->addColumn('action', function($row){

                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('customer.edit',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li class="dropdown-item"><a href="'.route('customer.show',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                            <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('customer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                        </ul>
                        ';
                        return $btn;    
                    })
                    ->rawColumns(['action','updated_at'])
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
            'password' => 'required|min:6|confirmed',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $data = [
            "name"      => $request->input('name'),
            "email"     => $request->input('email'),
            "phone"     => $request->input('phone'),
            "password"  => bcrypt($request->input('password')),
            "address"   => $request->input('address'),
            "userid"    => Str::random(6),
            "is_active" => $request->input('active')??0
        ];

        $insert = User::create($data);
        $insert->assignRole('user');

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $destination = 'images/user_profile';
            $file_name = time()."_".$insert->userid.".".$file->getClientOriginalExtension();
            $file->move($destination,$file_name);

            $user = User::find($insert->id);
            $user->profile_photo_path = $file_name;
            $user->save();
        }

        $causer = auth()->user();
        $atribut = ['attributes' => $data];

        activity('customer')->performedOn($insert)
                    ->causedBy($causer)
                    ->withProperties($atribut)
                    ->log('Pengguna melakukan penambahan pelanggan');
        
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
        $data = User::Role('user')->where('userid',$id)->first();
        $bank_accounts = UserBankAccount::where('user_id',$data->id)->get();
        return view('customer.detail',compact('data','bank_accounts'));
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
        $data = User::Role('user')->where('userid',$id)->first();
        return view('customer.edit',compact('data'));
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
        $user = User::where('userid', $id)->first();
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            "name"      => $request->input('name'),
            "email"     => $request->input('email'),
            "phone"     => $request->input('phone'),
            "address"   => $request->input('address'),
            "is_active" => $request->input('active')??0
        ];

        $user = User::Role('user')->where('userid', $id)->first();
        $update = $user->update($data);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $destination = 'images/user_profile';
            $file_name = time()."_".$user->userid.".".$file->getClientOriginalExtension();
            $file->move($destination,$file_name);

            $user->profile_photo_path = $file_name;
            $user->save();
        }

        if(!empty($request->input('password'))){

            $request->validate([
                'password' => 'required|min:6|confirmed'
            ]);

            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

        $causer = auth()->user();
        $atribut = ['attributes'=>$data];

        activity('customer')->performedOn($user)
                    ->causedBy($causer)
                    ->withProperties($atribut)
                    ->log('Pengguna melakukan pengubahan pelanggan');

        if($update){
            return redirect()->route('customer.index')
                        ->with('success','Data berhasil diubah');            
        }else{
            return redirect()->route('customer.index')
                        ->with('error','Opps, Terjadi kesalahan.');            
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
        $user = User::Role('user')->where('userid',$id)->first();
        
        $user->removeRole('user');

        $delete = $user->delete();

        $causer = auth()->user();
        $atribut = ['attributes'=> [
            "userid" => $user->userid
        ]];

        activity('customer')->performedOn($user)
                    ->causedBy($causer)
                    ->withProperties($atribut)
                    ->log('Pengguna melakukan penghapusan pelanggan');

        return Response()->json($delete);
    }

    public function export() 
    {
        return (new CustomerExport)->download('customer.xlsx');
        // return (new CustomerExport)->download('invoices.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        // return Excel::download(new CustomerExport, 'customer.xlsx');
        // return (new CustomerExport)->download('invoices.xlsx');
    }

    public function import() 
    {
        return view('customer/import');
    }

    public function storeImport(Request $request) 
    {
        $user = Excel::import(new CustomerImport, $request->file('excel'));

        return redirect()->route('customer.index')
                            ->with('success','Data berhasil ditambahkan');
    }
}
