<?php

namespace App\Http\Controllers\Admin;

use App\Models\CampaignYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CampaignYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        
        $search = $request->q;

        if (isset($search)) {
            //dd($request->search);
            $campaign_years = CampaignYear::whereRaw('CAST(calendar_year as char(4)) like ?', [ $search .'%'])->orderBy('calendar_year', 'desc')->paginate(10);

            $campaign_years->appends(['q' => $request->q ]);

        } else {
            // get all the record 
            $campaign_years = CampaignYear::orderBy('calendar_year', 'desc')->paginate(10);
        }   

        // load the view and pass the sharks
        return view('admin.campaignyears.index', compact('campaign_years','search'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // load the create form (app/views/sharks/create.blade.php)
        return view('admin.campaignyears.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validate
        // read more on validation at http://laravel.com/docs/validation
        $request->validate([
            'calendar_year'       => 'required|numeric',
            'number_of_periods'   => 'required|numeric|min:1|max:99',
            'status'              => 'required',
            'start_date'          => 'required|date|before:end_date',
            'end_date'            => 'required|date|after:start_date',
            'close_date'          => 'required|date',
        ]);

        // Addition Validation - Only one calendar year allow to be Active
        if ($request->status == 'A') {
            if ($cy = CampaignYear::where('Status', 'A')->orderByDesc('calendar_year')->first() ) {
                throw ValidationException::withMessages(
                    ['status' => 'The Calendar Year ' . $cy->calendar_year . ' is active.']
                );
            }
        }

            // store
            if (!(CampaignYear::where('calendar_year', $request->calendar_year)->exists())) {

                CampaignYear::Create([
                    'calendar_year' => $request->calendar_year,
                    'number_of_periods' =>  $request->number_of_periods,
                    'status' => $request->status,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'close_date' => $request->close_date,
                    'created_by_id' => Auth::id(),
                ]);

            } else {
                
                throw ValidationException::withMessages(
                    ['calendar_year' => 'Calendar Year already exits']
                );
                
            }

            // redirect
            //Session::flash('message', 'Successfully created shark!');
            return redirect()->route('campaignyears.index')
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

        // get the shark
        $campaign_year = CampaignYear::find($id);

        // show the view and pass the campaign year to it
        return view('admin.campaignyears.show', compact('campaign_year'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // get the shark
        $campaign_year = CampaignYear::find($id);

        // show the view and pass the campaign year to it
        return view('admin.campaignyears.create', compact('campaign_year'));
        
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
            'number_of_periods'   => 'required|numeric|min:1|max:99',
            'status'              => 'required',
            'start_date'          => 'required|date|before:end_date',
            'end_date'            => 'required|date|after:start_date',
            'close_date'          => 'required|date',
        ]);

        // Addition Validation - Only one calendar year allow to be Active
        if ($request->status == 'A') {
            if ($cy = CampaignYear::where('Status', 'A')->orderByDesc('calendar_year')->first()) {
                throw ValidationException::withMessages(
                    ['status' => 'The Calendar Year ' .$cy->calendar_year . ' is active.']
                );
            }
        }
    
        CampaignYear::where('id',$id)->update([
        
            'number_of_periods' =>  $request->number_of_periods,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'close_date' => $request->close_date,
            'modified_by_id' => Auth::id(),
        ]);

        return redirect()->route('campaignyears.index')
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
        //
    }
}
