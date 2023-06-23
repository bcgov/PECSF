<?php

namespace App\Http\Controllers\Admin;

use App\Models\CampaignYear;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CampaignYearRequest;
use Illuminate\Validation\ValidationException;

class CampaignYearController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');
         //$this->middleware('permission:post-create', ['only' => ['create', 'store']]);
         //$this->middleware('permission:post-create', ['except' => ['create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        if($request->ajax()) {

            $campignyears = CampaignYear::select(['campaign_years.*']);

            return Datatables::of($campignyears)
                ->addColumn('action', function ($campaign_year) {
                //return '<a href="#" class="notification-modal btn btn-xs btn-primary" value="'. $notification->id .'"><i class="glyphicon glyphicon-envelope"></i>View</a>';

                return '<a class="btn btn-info btn-sm" href="' . route('settings.campaignyears.show',$campaign_year->id) . '">Show</a>' .
                       '<a class="btn btn-primary btn-sm ml-2" href="' . route('settings.campaignyears.edit',$campaign_year->id) . '">Edit</a>';
            })
            ->make(true);
        }

        // get all the record 
        //$campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);

        // load the view and pass 
        return view('admin-campaign.campaignyears.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    
        return view('admin-campaign.campaignyears.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampaignYearRequest $request)
    {

        CampaignYear::Create([
            'calendar_year' => $request->calendar_year,
            'number_of_periods' =>  $request->number_of_periods,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'close_date' => $request->close_date,
            'created_by_id' => Auth::id(),
            'modified_by_id' => Auth::id(),
        ]);

        return redirect()->route('settings.campaignyears.index')
            ->with('success','Campaign Year ' . $request->calendar_year . ' created successfully');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $campaign_year = CampaignYear::find($id);

        // show the view and pass the campaign year to it
        return view('admin-campaign.campaignyears.show', compact('campaign_year'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $campaign_year = CampaignYear::find($id);

        // show the view and pass the campaign year to it
        return view('admin-campaign.campaignyears.create', compact('campaign_year'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CampaignYearRequest $request, $id)
    {
        $cy = CampaignYear::where('id',$id)->first();
        $cy->fill( $request->except(['calendar_year']) );
        $cy->modified_by_id = Auth::id();
        $cy->save();

        return redirect()->route('settings.campaignyears.index')
                ->with('success','Campaign Year ' . CampaignYear::find($id)->calendar_year . ' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Not implement for Campaign Year
    }
}
