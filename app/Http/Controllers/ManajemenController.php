<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\hasAnyRole;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManajemenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::Role(['admin','cs','superadmin'])->with('roles')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                        $btn = '<a href="'.route('manajement_account.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
                        
                        $btn .= ' <a href="'.route('manajement_account.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-warning btn-sm">Detail</a>';
                        if( !($row->hasRole('superadmin')) || ($row->id==auth()->user()->id) ){
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('manajement_account.delete',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
                        }

                        return $btn;

                            // $btn = '<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                            //             <span class="sr-only">Toggle Dropdown</span>
                            //                 Aksi 
                            //             <div class="dropdown-menu" role="menu">
                            //                 <a class="dropdown-item" href="'.route('service_order.edit',$row->id).'"><i class="fa fa-edit"></i> Ubah</a>
                            //                 <a class="dropdown-item btn_delete" data-url="'.route('service_order.delete.ajax',$row->id).'" href="'.route('dashboard').'"><i class="fa fa-times"></i> Hapus</a>
                            //             </div>
                            //         </button>';
                            // return $btn;
                    })
                    ->addColumn('role',function($row){
                        $template = '';
                        foreach ($row->roles as $key => $value) {
                            # code...
                            $template .= "<span class='badge badge-info mt-1'>".($value->name==='cs'?'customer service':$value->name)."</span><br>";
                        }
                        return $template;
                    })
                    ->addColumn('status',function($row){
                        if($row->is_active=="1"){
                            return "<span class='badge badge-success'>Aktif</span>";
                        }else{
                            return "<span class='badge badge-secondary'>Not Aktif</span>";
                        }
                    })
                    ->addColumn('created_at', function($row){
                        if(is_null($row->last_login)){
                            return '-';
                        }else{
                            return Carbon::parse($row->last_login)->format("d/m/Y H:i");
                        }
                    })
                    ->rawColumns(['action','status','role'])
                    ->make(true);
        }

        return view('manajemen_account.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('manajemen_account.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function super_admin()
    {
        
        $roles_db = Role::all()->pluck('name');
        if(!in_array('superadmin',$roles_db->toArray())){
            $role = Role::create(['name' => 'superadmin','guard_name'=>'web']);
        }

        try {
            //code...
            DB::beginTransaction();
                
            $data_user = [
                "name" => "super admin",
                "email" => "super@admin.com",
                "phone" => "081xxxxxxxxx",
                "password" => Hash::make(123456),
                "userid" => 111111,
                "address" => "Rumah Superadmin"
            ];
            
            $user = User::create($data_user);
    
            $user->assignRole('superadmin');

            echo "superadmin created";
    
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd("Error : ".$th->getMessage());
        }

    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:6|confirmed',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'role'  => 'required'
        ]);

        $cek = true;
        while ($cek) {
            # code...
            $userid = mt_rand(100000,999999);
            $cek_id = User::where('userid',$userid)->first();
            if(empty($cek_id)){
                $cek = false;
            }
        }

        try {
            //code...
            DB::beginTransaction();
            
            $data_user = [
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "phone" => $request->input('phone'),
                "password" => Hash::make($request->input('password')),
                "userid" => $userid,
                "address" => $request->input('address')
            ];
            
            $user = User::create($data_user);

            $roles = $request->input('role');
            $roles_db = Role::all()->pluck('name');
            if(!in_array('cs',$roles_db->toArray())){
                $role = Role::create(['name' => 'cs','guard_name'=>'web']);
            }

            foreach ($roles as $key => $value) {
                # code...
                if($value==="cs"){
                    $user->assignRole('cs');

                    $user = User::where('userid',$request->userid)->first();
            
                    $causer = auth()->user();
                    $atribut = [

                    ];
            
                    activity('add_role')->performedOn($user)
                                ->causedBy($causer)
                                ->withProperties($atribut)
                                ->log('Pengguna melakukan penambahan user CS');

                }elseif($value==="admin"){
                    $user->assignRole('admin');

                    $causer = auth()->user();
                    $atribut = [

                    ];
            
                    activity('add_role')->performedOn($user)
                                ->causedBy($causer)
                                ->withProperties($atribut)
                                ->log('Pengguna melakukan penambahan user Admin');
                }
            }


            DB::commit();

            return redirect()->route('manajement_account.index')
                            ->with('success','Akun Pengguna berhasil di tambahkan');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            return redirect()->route('manajement_account.index')
                                    ->with('error','Maaf terjadi kesalahan');
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
        $data = User::Role(['admin','cs','superadmin'])->where('id',$id)->with('roles')->first();
        if(is_null($data)){
            return redirect()->route('manajement_account.index')->with('error','Maaf terjadi kesalahan');
        }
        return view('manajemen_account.detail',compact('data'));
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
        $data = User::Role(['admin','cs','superadmin'])->where('id',$id)->with('roles')->first();
        if(is_null($data)){
            return redirect()->route('manajement_account.index')->with('error','Maaf terjadi kesalahan');
        }
        return view('manajemen_account.edit',compact('data'));
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
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'role'  => 'required'
        ]);

        try {
            //code...
            DB::beginTransaction();
            
            $data_user = [
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "phone" => $request->input('phone'),
                "address" => $request->input('address')
            ];
            
            $user = User::find($id);
            $user->update($data_user);

            $roles = $request->input('role');

            $roles_db = Role::all()->pluck('name');
            if(!in_array('cs',$roles_db->toArray())){
                $role = Role::create(['name' => 'cs','guard_name'=>'web']);
            }

            $user->syncRoles($roles);

            DB::commit();

            return redirect()->route('manajement_account.index')
                            ->with('success','Akun Pengguna berhasil di ubah');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            return redirect()->route('manajement_account.index')
                                    ->with('error','Maaf terjadi kesalahan');
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
        $user = User::Role(['admin','cs'])->where('id',$id)->first();
        $user_old = $user->toArray();
        try {
            //code...
            DB::beginTransaction();

            $user->removeRole('admin');
            $user->removeRole('cs');

            // $user->engineer->delete();
            $delete = $user->delete();

            $causer = auth()->user();
            $atribut = [
                "attributes" => $user_old
            ];
    
            activity('delete_role')
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan penghapusan pengguna');

            DB::commit();
            return Response()->json($delete);
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return Response()->json($th->getMessage());
        }
    }
}
