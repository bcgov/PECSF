<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;


class CityRegionInformationController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        if($request->ajax()) {

            $cities = City::select('cities.*', 'regions.code as region_code', 'regions.name as region_name')
                            ->leftjoin('regions', 'regions.code', 'cities.TGB_REG_DISTRICT');

            return Datatables::of($cities)
                    ->make(true);
        }

        return view('admin-campaign.cities.index');

    }

}
