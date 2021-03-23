<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mapper;
use App\Models\CategoryService;
use App\Models\Service;
use App\Models\User;
use \DateTime;
use \DateInterval;
use \DatePeriod;

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
        Mapper::map(-7.4181887466077265, 109.22154831237727,['zoom'=>10]);

        $locationEngineers = User::Role('teknisi')->whereNotNull('lat')->whereNotNull('lng')->get();

        foreach($locationEngineers as $location){
            Mapper::informationWindow($location->lat, $location->lng, $location->name, []);
        }

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

    public function get_statistik_engineer_register(Request $request)
    {
        $filter = $request->filter;

        if($filter === "day"){
            $date = new DateTime(date('Y-m-01'));
            $end_date = new DateTime(date('Y-m-t'));

            $label = [];
            $data = [];
            for ($i=$date; $i <= $end_date; $i->modify('+1 day')) { 
                # code...
                $user = User::Role('teknisi')
                                ->whereDate('created_at',$i->format('Y-m-d'))
                                ->count();
                $label[] = $i->format('d M Y');
                $data[] = (int)$user;
            }

            $reponse = [
                "success" => true,
                "label" => $label,
                "data" => $data
            ];
            return response()->json($reponse);
        }elseif($filter === "month"){

            $date = new DateTime(date('Y-01-01'));
            $end_date = new DateTime(date('Y-12-t'));
            $interval = new DateInterval('P1M');
            $periode = new DatePeriod($date,$interval,$end_date);

            $label = [];
            $data = [];

            foreach ($periode as $key => $dt) {
                # code...
                // echo $dt->format('Y-m')." ";
                $user = User::Role('teknisi')
                                ->whereMonth('created_at',$dt->format('m'))
                                ->count();
                $label[] = $dt->format('M Y');
                $data[] = (int)$user;
            }
            $reponse = [
                "success" => true,
                "label" => $label,
                "data" => $data
            ];
            return response()->json($reponse);
        }
    }
}
