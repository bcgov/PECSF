<?php

namespace App\Http\Controllers\Admin;

use File;
use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\SpecialCampaign;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SpecialCampaignSetupRequest;


class SpecialCampaignSetupController extends Controller
{

    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');

         $this->image_folder = "img/uploads/special_campaign";

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

            $special_campaigns = SpecialCampaign::join('charities', 'charities.id', 'special_campaigns.charity_id')
                ->when($request->term , function($query) use($request) {
                    return $query->where('special_campaigns.name','LIKE','%'.$request->term.'%')
                            ->orWhere('special_campaigns.description','LIKE','%'.$request->term.'%')
                            ->orWhere('special_campaigns.banner_text','LIKE','%'.$request->term.'%')
                            ->orWhere('charities.charity_name','LIKE','%'.$request->term.'%')
                            ;
                })
                ->when($request->year, function($query) use($request) { 
                    return $query->whereYear('start_date', '=', $request->year )
                                ->orWhereYear('end_date', '=', $request->year );
                })
                ->when($request->bn, function($query) use($request) {
                    return $query->where('charities.registration_number','LIKE','%'.$request->bn.'%');
                })
                ->select('special_campaigns.*')
                ->with('Charity');

            return Datatables::of($special_campaigns)
                ->addColumn('action', function ($special_campaign) {
                return '<a class="btn btn-info btn-sm  show-bu" data-id="'. $special_campaign->id .'" >Show</a>' .
                       '<a class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $special_campaign->id .'" >Edit</a>' .
                       '<a class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $special_campaign->id .
                       '" data-name="'. $special_campaign->name . '">Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin-campaign.special-campaigns.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpecialCampaignSetupRequest $request)
    {
        //
        if ($request->ajax()) {

            // file handling
            $file = $request->file('logo_image_file');
            $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );
            $file->move(public_path( $this->image_folder ), $filename);
            
            $special_campaign = SpecialCampaign::Create([

                'name' => $request->name,
                'description' => $request->description,
                'banner_text' => $request->banner_text,
                'charity_id' => $request->charity_id,
                'start_date' => $request->start_date,
                'end_date' =>$request->end_date,
                'image' => $filename,
                
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),

            ]);

            return response()->json($special_campaign);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        if ($request->ajax()) {

            $special_campaign = SpecialCampaign::where('id', $id)->with('charity')->first();

            $special_campaign->created_by_name = $special_campaign->created_by ? $special_campaign->created_by->name : '';
            $special_campaign->updated_by_name = $special_campaign->updated_by ? $special_campaign->updated_by->name : '';
            $special_campaign->formatted_created_at = $special_campaign->created_at->format('Y-m-d H:i:s');
            $special_campaign->formatted_updated_at = $special_campaign->updated_at->format('Y-m-d H:i:s');

            unset($special_campaign->created_by );
            unset($special_campaign->updated_by );
            return response()->json($special_campaign);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $special_campaign = SpecialCampaign::where('id', $id)->with('charity')->first();
            return response()->json($special_campaign);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SpecialCampaignSetupRequest $request, $id)
    {
        if ($request->ajax()) {

            // file handling
        
            $new_filename = '';
            if ($request->file('logo_image_file')) {
                $file = $request->file('logo_image_file');
                $new_filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );
                $file->move(public_path( $this->image_folder ), $new_filename);
            }

            $special_campaign = SpecialCampaign::where('id', $id)->first();
            $special_campaign->fill( $request->validated() );

            if ($new_filename) {

                // Clean up old file
                if ($special_campaign->image) {
                    $old_filename = public_path( $this->image_folder ).'/'.$special_campaign->image;
                    if (File::exists( $old_filename )) {
                        File::delete( $old_filename );
                    }
                }

                $special_campaign->image = $new_filename;
            }
            $special_campaign->updated_by_id = Auth::id();
            $special_campaign->save();

            return response()->json($special_campaign);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $special_campaign = SpecialCampaign::where('id', $id)->first();

        // Clean up old file
        if ($special_campaign->image) {
            $old_filename = public_path( $this->image_folder ).'/'.$special_campaign->image;
            if (File::exists( $old_filename )) {
                File::delete( $old_filename );
            }
        }
        $special_campaign->delete();

        return response()->noContent();
    }


    public function getCharities(Request $request) {


        $charities = Charity::orderBy('charity_name','asc')->select('id','charity_name','registration_number')
            ->when( $request->q , function ($q) use($request) {
                return $q->whereRaw("LOWER(charity_name) LIKE '%" . strtolower($request->q) . "%'")
                         ->orWhereRaw("LOWER(registration_number) LIKE '%" . strtolower($request->q) . "%'");

            })
            ->where('charity_status','Registered')
            ->limit(100)
            ->get();

        $formatted_charities = [];
        foreach ($charities as $charity) {
            $formatted_charities[] = ['id' => $charity->id, 
                                    'text' => $charity->charity_name . ' (' . $charity->registration_number . ')',
                                    'bn' => $charity->registration_number  ];
        }

        return response()->json($formatted_charities);
    }

}
