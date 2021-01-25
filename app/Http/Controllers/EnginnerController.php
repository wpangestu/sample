<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceOrder;
use App\Models\Engineer;
use DataTables;
use Illuminate\Support\Str;
use Mapper;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class EnginnerController extends Controller
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
            $data = User::latest()->Role('teknisi')->where('verified',true)->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                            $btn = '<a href="'.route('engineer.edit',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
                            $btn .= ' <a href="'.route('engineer.show',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('engineer.index');
    }

    public function confirmation(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest()
                            ->Role('teknisi')
                            ->where('verified',0)
                            // ->whereHas('engineer', function (Builder $query) {
                            //         $query->where('is_varified_email',1);
                            // })
                            ->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($row){
                            if($row->engineer==null){
                                $badge = 'null';
                            }
                            elseif($row->engineer->status==="pending"){
                                $badge = '<span class="badge badge-warning">Pending</span>';
                            }
                            elseif($row->engineer->status==="decline"){
                                $badge = '<span class="badge badge-danger">Ditolak</span>';
                            }
                            else{
                                $badge = '-';
                            }
                            return $badge;
                    })
                    ->addColumn('action', function($row){
                            if($row->engineer==null){
                                $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
                            }else{
                                $btn = ' <a href="'.route('engineer.confirm.detail',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Detail" class="edit btn btn-info btn-sm">Detail</a>';
                                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
                            }
                            return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);        
        }
        return view('engineer.index_confirm');
    }

    public function show_confirmation($id)
    {
        $data = User::Role('teknisi')->where('userid',$id)->first();
        return view('engineer.detail_confirm',compact('data'));
    }

    public function accept_engineer($id)
    {
        try {
            //code...
            DB::beginTransaction();

            $user = User::Role('teknisi')->where('userid',$id)->first();
            $user->verified = 1;
            $user->save();
            $user->engineer->is_verified_data = 1;
            $user->engineer->verified_data_at = date('Y-m-d H:i:s');
            $user->engineer->verified_by = Auth::user()->id;
            $user->engineer->status = 'success';
            $user->engineer->save();

            DB::commit();

            return redirect()->route('engineer.index')
                            ->with('success','Teknisi berhasil diverifikasi');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th.getMessaage());
        }

    }

    public function decline_engineer($id)
    {
        try {
            //code...
            DB::beginTransaction();

            $user = User::Role('teknisi')->where('userid',$id)->first();
            $user->verified = 0;
            $user->save();

            $user->engineer->is_verified_data = 0;
            $user->engineer->verified_data_at = date('Y-m-d H:i:s');
            $user->engineer->verified_by = Auth::user()->id;
            $user->engineer->status = 'decline';
            $user->engineer->save();

            DB::commit();

            return redirect()->route('engineer.confirm.index')
                            ->with('success','Berhasil update verifikasi teknisi');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th.getMessaage());
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        return view('engineer.create');
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
            'id_card_number' => 'required|numeric'
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

        $otp = mt_rand(1000,9999);

        try {
            //code...
            DB::beginTransaction();

            $data = [
                "name"      => $request->input('name'),
                "email"     => $request->input('email'),
                "phone"     => $request->input('phone'),
                "password"  => Hash::make($request->input('password')),
                "address"   => $request->input('address'),
                "userid"    => $userid,
                "is_active" => $request->input('active')??0,
                "lat"       => $request->input('lat'),
                "lng"       => $request->input('lng'),
                "otp"       => $otp
            ];
    
            $insert = User::create($data);
            $insert->assignRole('teknisi');
    
            $engineer = Engineer::create([
                "id_card_number"    => $request->input('id_card_number'),
                "name"              => $request->input('name'),
                "email"             => $request->input('email'),
                "phone"             => $request->input('phone'),
                "address"           => $request->input('address'),
                "user_id"           => $insert->id,
            ]);
    
            if ($request->hasFile('photo')) {
    
                $uploadFolder = 'users/photo';
                $photo = $request->file('photo');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $insert->profile_photo_path = Storage::disk('public')->url($photo_path);
                $insert->save();
            }

            if($request->hasFile('id_card_image')){
                $uploadFolder = 'users/card_id';
                $id_card_image = $request->file('id_card_image');
                $id_card_image_path = $id_card_image->store($uploadFolder, 'public');
                $engineer->id_card_image = Storage::disk('public')->url($id_card_image_path);
                $engineer->save();
            }

            if($request->hasFile('id_card_selfie_image')){
                $uploadFolder = 'users/selfie_card_id';
                $id_card_selfie_image = $request->file('id_card_selfie_image');
                $id_card_selfie_path = $id_card_selfie_image->store($uploadFolder,'public');
                $engineer->id_card_selfie_image = Storage::disk('public')->url($id_card_selfie_path);
                $engineer->save();
            }

            DB::commit();

            if($insert){
                return redirect()->route('engineer.index')
                            ->with('success','Data berhasil ditambahkan');
            }else{
                return redirect()->route('engineer.index')
                            ->with('error','Opps, Terjadi kesalahan.');
            }

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
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
        $data = User::Role('teknisi')->where('userid',$id)->first();
        $service_orders = ServiceOrder::where('engineer_id',$data->id)->orderBy('created_at','desc')->get();

        if(!empty($data->lat) && !empty($data->lng)){
            Mapper::map($data->lat, $data->lng,['zoom'=>14]);
            Mapper::informationWindow($data->lat, $data->lng, $data->name, []);
        }
        return view('engineer.detail2',compact('data','service_orders'));
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
        $data = User::Role('teknisi')->where('userid',$id)->first();
        if(!empty($data->lat) && !empty($data->lng)){
            Mapper::map($data->lat, $data->lng,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        }else{
            Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        }
        return view('engineer.edit',compact('data'));
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
        $user = User::Role('teknisi')->where('userid', $id)->first();
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
            "is_active" => $request->input('active')??0,
            "lat"       => $request->input('lat'),
            "lng"       => $request->input('lng'),
        ];

        $user = User::Role('teknisi')->where('userid', $id)->first();
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

        if($update){
            return redirect()->route('engineer.index')
                        ->with('success','Data berhasil diubah');            
        }else{
            return redirect()->route('engineer.index')
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
        $user = User::Role('teknisi')->where('userid',$id)->first();

        try {
            //code...
            DB::beginTransaction();

            $user->removeRole('teknisi');

            $user->engineer->delete();
            $delete = $user->delete();

            DB::commit();
            return Response()->json($delete);
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return Response()->json($th->getMessage());
        }
        


    }
}
