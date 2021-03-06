<?php

namespace App\Http\Controllers;

use DB;
use Mapper;
use DataTables;
use App\Models\User;
use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Engineer;
use App\Models\Province;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use App\Models\UserBankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
                    ->addColumn('updated_at', function($row){
                        return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                    })
                    ->addColumn('action', function($row){
   
                            $btn = '
                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item"><a href="'.route('engineer.edit',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                                <li class="dropdown-item"><a href="'.route('engineer.show',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                                <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                            </ul>
                            ';
                            return $btn;    
                    })
                    ->rawColumns(['action','updated_at'])
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
                    ->addColumn('updated_at', function($row){
                        return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                    })
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

                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                        ';
                        //     <li class="dropdown-item"><a href="'.route('engineer.edit',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                        //     <li class="dropdown-item"><a href="'.route('engineer.show',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        //     <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                        // </ul>
                        // ';

                            if($row->engineer==null){
                                $btn .= ' 
                                <li class="dropdown-item">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn_delete">Delete</a>
                                </li>
                                    ';
                            }else{
                                $btn .= ' 
                                <li class="dropdown-item">
                                    <a href="'.route('engineer.confirm.detail',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Detail" class="edit"><i class="fa fa-info-circle"></i> Detail</a>
                                </li>    
                                ';
                                $btn .= '
                                <li class="dropdown-item">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('engineer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a>
                                </li>';
                            }
                            $btn .= '</ul>';
                            return $btn;
                    })
                    ->rawColumns(['action','status','updated_at'])
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
            $user_old_verified = $user->verified;
            $user->verified = 1;
            $user->save();
            $user->engineer->is_verified_data = 1;
            $user->engineer->verified_data_at = date('Y-m-d H:i:s');
            $user->engineer->verified_by = Auth::user()->id;
            $user->engineer->status = 'success';
            $user->engineer->save();

            $causer = auth()->user();
            $atribut = [
                "attributes" => ["verified" => $user->verified],
                "old" => ["verified" => $user_old_verified]
            ];

            activity('confirm_engineer')->performedOn($user)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan konfirmasi ACC Teknisi');

            DB::commit();

            return redirect()->route('engineer.index')
                            ->with('success','Teknisi berhasil diverifikasi');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
        }

    }

    public function decline_engineer($id)
    {
        try {
            //code...
            DB::beginTransaction();

            $user = User::Role('teknisi')->where('userid',$id)->first();
            $user_old_verified = $user->verified; 
            $user->verified = 0;
            $user->save();

            $user->engineer->is_verified_data = 0;
            $user->engineer->verified_data_at = date('Y-m-d H:i:s');
            $user->engineer->verified_by = Auth::user()->id;
            $user->engineer->status = 'decline';
            $user->engineer->save();

            $causer = auth()->user();
            $atribut = [
                "attributes" => ["verified" => $user->verified],
                "old" => ["verified" => $user_old_verified]
            ];
            activity('confirm_engineer')->performedOn($user)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan konfirmasi Tolak Teknisi');

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
        $provinces = Province::all();
        Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        return view('engineer.create',compact('provinces'));
    }

    public function getListRegency(Request $request){
        $province_id = $request->input('province_id');
        $regency = Regency::where('province_id',$province_id)->get();
        return json_encode($regency);
    }

    public function getListDistict(Request $request){
        $regency_id = $request->input('regency_id');
        $district = District::where('regency_id',$regency_id)->get();
        return json_encode($district);
    }

    public function getListVillage(Request $request){
        $district_id = $request->input('district_id');
        $village = Village::where('district_id',$district_id)->get();
        return json_encode($village);
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
                "otp"       => $otp,
                'province_id' => $request->input('province_id'),
                'regency_id' => $request->input('regency_id'),
                'district_id' => $request->input('district_id'),
                'village_id' => $request->input('village_id'),
            ];
            // dd($data);            
            $insert = User::create($data);
            $insert->assignRole('teknisi');

            if(!(is_null($request->input('lat'))) && !(is_null($request->input('lng'))) ){
                $user_address = [
                    "lat" => $request->input('lat'),
                    "lng" => $request->input('lng'),
                    "user_id" => $insert->id,
                    "name" => "Alamat Utama",
                    "address" => $request->input('map_address')
                ];
                UserAddress::create($user_address);
            }
    
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

            $causer = auth()->user();
            $atribut = ['attributes' => $data];
    
            activity('customer')->performedOn($engineer)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan penambahan teknisi');

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
            // dd($th->getMessage());
            return redirect()->route('engineer.index')
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
        $data = User::Role('teknisi')->where('userid',$id)->first();
        $service_orders = ServiceOrder::where('engineer_id',$data->id)->orderBy('created_at','desc')->get();

        if(!empty($data->lat) && !empty($data->lng)){
            Mapper::map($data->lat, $data->lng,['zoom'=>14]);
            Mapper::informationWindow($data->lat, $data->lng, $data->name, []);
        }
        $bank_accounts = UserBankAccount::where('user_id',$data->id)->get();
        return view('engineer.detail2',compact('data','service_orders','bank_accounts'));
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
        if(is_null($data)){
            return redirect()->route('engineer.index')
                                ->with('error','Data Tidak ditemukan.');            
        }
        $provinces = Province::all();
        $regency = Regency::where('province_id',$data->province_id)->get();
        $district = District::where('regency_id',$data->regency_id)->get();
        $village = Village::where('district_id',$data->district_id)->get();
        if(!empty($data->lat) && !empty($data->lng)){
            Mapper::map($data->lat, $data->lng,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        }else{
            Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>14,'marker' => true, 'draggable' => true, 'eventDrag' => 'updateLatlang(event.latLng.lat(),event.latLng.lng());']);
        }
        return view('engineer.edit',compact('data','provinces','regency','district','village'));
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
            "province_id" => $request->input('province_id'),
            "regency_id" => $request->input('regency_id'),
            "district_id" => $request->input('district_id'),
            "village_id" => $request->input('village_id'),
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

        $causer = auth()->user();
        $atribut = ['attributes' => $data];

        activity('customer')->performedOn($user)
                    ->causedBy($causer)
                    ->withProperties($atribut)
                    ->log('Pengguna melakukan pengubahan teknisi');

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

            $causer = auth()->user();
            $atribut = ['attributes' => [
                "userid" => $user->userid
            ]];
    
            activity('customer')->performedOn($user)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan pengubahan teknisi');

            DB::commit();
            return Response()->json($delete);
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return Response()->json($th->getMessage());
        }
        


    }
}
