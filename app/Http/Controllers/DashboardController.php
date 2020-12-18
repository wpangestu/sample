<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mapper;
use App\Models\CategoryService;
use App\Models\Service;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>14]);
        Mapper::informationWindow(-7.414858422515413, 109.23044349947345, 'Service HP Purwokerto', []);
        Mapper::informationWindow(-7.424652958349921, 109.23028470725927, 'Erafone', []);
        Mapper::informationWindow(-7.433579789287531, 109.24881126732865, 'Multi Cellular', []);
        Mapper::informationWindow(-7.419508203216844, 109.24469139415676, 'Mukti Cell', []);

        $numCategoryServices = CategoryService::where('status',1)->count();
        $numServices = Service::count();
        $numCustomer = User::Role('user')->where('is_active',1)->count();
        $numEngineer = User::Role('teknisi')->where('is_active',1)->count();

        return view('dashboard/index',compact('numCategoryServices','numServices','numCustomer','numEngineer'));
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
