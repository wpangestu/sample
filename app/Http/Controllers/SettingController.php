<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use App\Models\TermOfService;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function privacyPolicy()
    {
        $privacyPolicy = PrivacyPolicy::first();
        return view('setting.privacy_policy', compact('privacyPolicy'));
    }

    public function storePrivacyPolicy(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        
        $insert = PrivacyPolicy::create($request->all());

        if($insert){
            return redirect()->route('setting.privacy_policy')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('setting.privacy_policy')
                        ->with('error','Opps, Terjadi kesalahan.');
        }
    }

    public function updatePrivacyPolicy(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        
        $update = PrivacyPolicy::find($id)->update($request->all());

        if($update){
            return redirect()->route('setting.privacy_policy')
                        ->with('success','Data berhasil diubah');
        }else{
            return redirect()->route('setting.privacy_policy')
                        ->with('error','Opps, Terjadi kesalahan.');
        }
    }

    public function termOfService()
    {
        $termOfService = TermOfService::first();
        return view('setting.term_of_service', compact('termOfService'));
    }

    public function storeTermOfService(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        
        $insert = TermOfService::create($request->all());

        if($insert){
            return redirect()->route('setting.term_of_service')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('setting.term_of_service')
                        ->with('error','Opps, Terjadi kesalahan.');
        }
    }

    public function updateTermOfService(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        
        $update = TermOfService::find($id)->update($request->all());

        if($update){
            return redirect()->route('setting.term_of_service')
                        ->with('success','Data berhasil diubah');
        }else{
            return redirect()->route('setting.term_of_service')
                        ->with('error','Opps, Terjadi kesalahan.');
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
