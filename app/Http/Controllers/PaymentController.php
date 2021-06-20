<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\HistoryBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $payment = Payment::latest()->get();
        return view('payment.index',compact('payment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $order = Order::where('order_number',$id)->first();
        $convenience_fee = random_int(100,999);
        return view('payment.create',compact('convenience_fee','order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id_order)
    {
        //
        // dd($request->all());

        $order = Order::find($id_order);

        if(is_null($order)){
            return redirect()->route('payment.index')->with('error','Data tidak ditemukan');
        }
        
        $request->validate([
            'order_id' => 'required',
            'total' => 'required',
            'unique_number' => 'required',
            'price' => 'required',
            'payment_method' => 'required',
            'payment_gateway'  => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            //code...

            if ($request->hasFile('image')) {

                $uploadFolder = 'users/payment/order';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $url = Storage::disk('public')->url($photo_path);
                // $service->save();
            }
    
            $data = [
                "customer_id" => $order->customer->id,
                "amount" => $request->input('price'),
                "paymentid" => "P".uniqid(),
                "convenience_fee" => $request->unique_number,
                "type" => $request->input('payment_gateway'),
                "orders" => json_encode($order->id),
                "image" => $url
            ];
    
            $payment = Payment::create($data);

            return redirect()->route('payment.index')->with('success','Pembayaran berhasil di update');
            
        } catch (\Throwable $th) {
            //throw $th;
            
            dd($th->getMessage());
            return redirect()->route('paymnet.index')->with('error','Terjadi kesalahan');


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
        $payment = Payment::find($id);
        if(is_null($payment)){
            return redirect()->back()->with('error','Data tidak ditemukan');
        }

        return view('payment.detail',compact('payment'));
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
        // $payment = Payment::where('paymentid',$id)->first();
        $payment = Payment::find($id);
        if(is_null($payment)){
            return redirect()->back()->with('error','Data tidak ditemukan');
        }

        // $order = Order::where('order_number',$id)->first();
        $convenience_fee = random_int(100,999);

        return view('payment.update',compact('convenience_fee','payment'));
    }

    public function confirm_accept($id){
        
        try {
            //code...

            DB::beginTransaction();

            $payment = Payment::find($id);

            $payment->status = 'success';
            $payment->verified_by = auth()->user()->id;
            $payment->verified_at = date('Y-m-d H:i:s');
            $payment->verified_name = auth()->user()->name;
            $payment->save();

            if($payment->type_payment=="order"){
                $orders = $payment->data_id;
                $order = Order::where('order_number',$orders)->first();
                $order->order_status = "payment_success";
                $order->save();
            }

            $amount = $payment->amount;
            $description = "";            
            if($payment->type_payment=="order"){
                $description = "Pemabayaran Order #".$payment->data_id;
            }else{
                $description = "Pemabayaran Deposit #".$payment->data_id;
            }

            $user = User::find($payment->customer_id);
            $user->balance = $user->balance+$amount;
            $user->save();

            HistoryBalance::create([
                "user_id" => $user->id,
                "amount" => $amount,
                "description" => $description,
                "created_by" => auth()->user()->id
            ]);

            $causer = auth()->user();
            $atribut = [

            ];

            DB::commit();

            activity('confirm_payment')->performedOn($payment)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan konfirmasi ACC Pembayaran');

            return redirect()->route('payment.index')->with('success','Data berhasil diubah');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            dd($th->getMessage());
        }

    }

    public function confirm_decline($id){
        
        try {
            //code...
            $payment = Payment::find($id);

            $payment->status = 'decline';
            $payment->verified_by = auth()->user()->id;
            $payment->verified_at = date('Y-m-d H:i:s');
            $payment->verified_name = auth()->user()->name;
            $payment->save();

            $causer = auth()->user();
            $atribut = [

            ];

            activity('confirm_payment')->performedOn($payment)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan konfirmasi Tolak Pembayaran');

            return redirect()->route('payment.index')->with('success','Pembayaran berhasil ditolak');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }

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
            'order_id' => 'required',
            'total' => 'required',
            'unique_number' => 'required',
            'price' => 'required',
            'payment_method' => 'required',
            'payment_gateway'  => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {

                $uploadFolder = 'users/payment/order';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $url = Storage::disk('public')->url($photo_path);
                // $service->save();
            }
    
            $data = [
                "status" => "check",
                "convenience_fee" => $request->unique_number,
                "image" => $url,
                "payment_gateway" => $request->payment_gateway
            ];
            
            Payment::find($id)->update($data);
            
            return redirect()->route('payment.index')->with('success','Data berhasil di ubah');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            return redirect()->back()->with('error','Maaf terjadi kesalahan');
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
    }
}
