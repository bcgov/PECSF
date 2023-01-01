<?php

namespace App\Http\Controllers\Admin;

use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\ProcessHistory;
use App\Jobs\CharitiesExportJob;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CRACharityRequest;

class CRACharityController extends Controller
{
    //
    //
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

            $charities = $this->getCharityQuery($request);

            // $charities = Charity::when( $request->registration_number, function($query) use($request) {
            //                         $query->where('charities.registration_number', 'like', '%'. $request->registration_number .'%');
            // })
            // ->when( $request->charity_name, function($query) use($request) {
            //     $query->where('charities.charity_name', 'like', '%'. $request->charity_name .'%');
            // })
            // ->when( $request->charity_status, function($query) use($request) {
            //     $query->where('charities.charity_status', 'like', '%'. $request->charity_status .'%');
            // })
            // ->when( $request->effdt, function($query) use($request) {
            //     $query->where('charities.effective_date_of_status', '>=', $request->effdt);
            // })
            // ->when( $request->designation_code, function($query) use($request) {
            //     $query->where('charities.designation_code', $request->designation_code);
            // })
            // ->when( $request->category_code, function($query) use($request) {
            //     $query->where('charities.category_code', $request->category_code );
            // })
            // ->when( $request->province, function($query) use($request) {
            //     $query->where('charities.province', $request->province);
            // })
            // ->orderBy('charity_name');

            return Datatables::of($charities)
                ->addColumn('effdt', function ($charity) {
                    return $charity->effective_date_of_status->format('d/m/Y');
                })
                ->addColumn('action', function ($charity) {
                return '<a class="btn btn-info btn-sm ml-2 mt-1 show-charity" data-id="'. $charity->id .'" >Show</a>' . 
                       '<a class="btn btn-primary btn-sm ml-2 mt-1 edit-charity" data-id="'. $charity->id .'" >Edit</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        $charity_status_list = Charity::charity_status_list();
        $designation_list = Charity::DESIGNATION_LIST;
        $category_list = Charity::CATEGORY_LIST;
        $province_list = Charity::PROVINCE_LIST;

        return view('admin-campaign.charities.index', compact('charity_status_list', 'designation_list', 'category_list', 'province_list'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CRACharityRequest $request)
    {

     
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

            $charity = Charity::where('id', $id)->first();

            $charity->effdt = $charity->effective_date_of_status ? $charity->effective_date_of_status->format('d/m/Y') : '';
            $charity->created_by_name = $charity->created_by ? $charity->created_by->name : '';
            $charity->updated_by_name = $charity->updated_by ? $charity->updated_by->name : '';
            $charity->formatted_created_at = $charity->created_at ? $charity->created_at->format('Y-m-d H:i:s') : '';
            $charity->formatted_updated_at = $charity->updated_at ? $charity->updated_at->format('Y-m-d H:i:s') : '';
            
            return response()->json($charity);
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
            
            $charity = Charity::where('id', $id)->first();
            $charity->effdt = $charity->effective_date_of_status ? $charity->effective_date_of_status->format('d/m/Y') : '';

            return response()->json($charity);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CRACharityRequest $request, $id)
    {

        // return ($request->validated);
        if ($request->ajax()) {
            $charity = Charity::where('id', $id)->first();

            $charity->fill( $request->validated() );

            $charity->use_alt_address = $request->exists('use_alt_address') ? true : false;
            $charity->updated_by_id = Auth::id();
            
            $charity->save();
        
            return response()->json($charity);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $charity = Charity::where('id', $id);
        // $charity->delete();

        // return response()->noContent();
    }

    public function exportProgress(Request $request, $id) {

// storage batch id in session
// get status 
        $history = ProcessHistory::where('id', $id)->first();

        if ($history) {

            // $batch_id = session()->get('charities-export-batch-id');


            // $batch = Bus::findBatch($batch_id);

            $finished = false;
            $message = 'Procsssing..., please wait.' . now();

            if ($history->status == 'Completed') {
                $finished = true;
                $link = route('settings.charities.download-export-file', $history->id);
                $message = 'Done. Download file <a class="" href="'.$link.'">here</a>';
            } else if ($history->status == 'Queued') {
                $message = 'Queued, please wait.';
            } else if ($history->status == 'Processing') {
                $progress = round(($history->done_count / $history->total_count) * 100,0);
                $message = 'Procsssing... ('. $progress .'%) , please wait.';
            } else {
                // others
            }

            return response()->json([
                'finished' => $finished,
                'message' => $message,
            ], 200);
        }   

    }

    public function downloadExportFile(Request $request, $id) {

        $history = ProcessHistory::where('id', $id)->first();
        // $path = Student::where("id", $id)->value("file_path");
    
        $filepath = $history->filename; 

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/csv',
            "Content-Transfer-Encoding: UTF-8",
        ];

        // return Storage::download($path);
        return Storage::disk('public')->download($filepath, $filepath, $headers); 

    }        



    public function export2csv(Request $request) {


        if($request->ajax()) {

            $filters = $request->all(); 

            // Submit a Job
            $history = \App\Models\ProcessHistory::create([
                'batch_id' => 0,
                'process_name' => 'CharitiesExportJob',
                'parameters' => json_encode( $filters ),
                'status'  => 'Queued',
                'submitted_at' => now(),
                'original_filename' => '',
                'filename' => '',
                'total_count' => 0,
                'done_count' => 0,
                'created_by_id' => Auth::Id(),
                'updated_by_id' => Auth::Id(),
            ]);

            // $filename = 'export_charities_'.now()->format("Y-m-d-hmsu").".csv";
            // $filePath = storage_path('app/public/'.$filename);
        

            $batch = Bus::batch([
                new CharitiesExportJob($history->id, $filters ),
            ])->dispatch();

            // dd ($batch->id);
            $history->batch_id = $batch->id;
            $history->save();


            session()->put('charities-export-batch-id', $batch->id );
// TODO 
// storage batch id in session

            return response()->json([
                    'batch_id' => $history->id,
            ], 200);
            

        }
        

        $filename = 'export_charities_'.date("Y-m-d").".csv";
        $handle = fopen( storage_path('app/public/'.$filename), 'w');

        $header = ['BN', 'Name', 'Status', 'Type of Donee', 'Effective Date', 
                    'Designation', 'Type', 'Category', 'Address', 'City', 'Province', 'Country', 'Postal', 
                    'Use Alt Address', 'Alt Address 1', 'Alt Address 2','Alt City', 'Alt Province', 'Alt Country', 'Alt Postal',
                    'Financial Contact Name','Financial Contact Title','Financial Contact Email'
                  ];

        $fields = ['registration_number','charity_name','charity_status','type_of_qualified_donee','effdt',
                   'designation_name','charity_type','category_name','address','city','province','country','postal_code',
                   'use_alt_address','alt_address1','alt_address2','alt_city','alt_province','alt_country','alt_postal_code',
                   'financial_contact_name','financial_contact_title','financial_contact_email'
                ];
    

        // Export header
        fputcsv($handle, ['Report Title     :  Eligible Employee Report'] );
        fputcsv($handle, ['Report Run on    : ' . now() ] );
        fputcsv($handle, [''] );
        fputcsv($handle, $header );
        // export the data with filter selection
        $sql = $this->getCharityQuery($request); 

        set_time_limit(240);

        $sql->chunk(2000, function($charities) use ($handle, $fields) {

                // additional data 
                foreach( $charities as $charity) {
                    // $charity->business_unit_name = $charity->bus_unit->name;
                    // $charity->region_name = $charity->region->name;
                    $charity->effdt = $charity->effective_date_of_status ?  $charity->effective_date_of_status->format('m-d-Y') : null;
                }

                $subset = $charities->map->only( $fields );
//  dd( $subset);
                // output to csv
                foreach($subset as $charity) {
                    fputcsv($handle, $charity, ',', '"' );
                }
        });

        fclose($handle);

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/csv',
            "Content-Transfer-Encoding: UTF-8",
        ];

        return Storage::disk('public')->download($filename, $filename, $headers); 

    }

    function getCharityQuery(Request $request) {

        $sql = Charity::when( $request->registration_number, function($query) use($request) {
                    $query->where('charities.registration_number', 'like', '%'. $request->registration_number .'%');
                })
                ->when( $request->charity_name, function($query) use($request) {
                    $query->where('charities.charity_name', 'like', '%'. $request->charity_name .'%');
                })
                ->when( $request->charity_status, function($query) use($request) {
                    $query->where('charities.charity_status', 'like', '%'. $request->charity_status .'%');
                })
                ->when( $request->effdt, function($query) use($request) {
                    $query->where('charities.effective_date_of_status', '>=', $request->effdt);
                })
                ->when( $request->designation_code, function($query) use($request) {
                    $query->where('charities.designation_code', $request->designation_code);
                })
                ->when( $request->category_code, function($query) use($request) {
                    $query->where('charities.category_code', $request->category_code );
                })
                ->when( $request->province, function($query) use($request) {
                    $query->where('charities.province', $request->province);
                })
                ->when( $request->use_alt_address == 'Y', function($query) use($request) {
                    $query->where('use_alt_address', '1');
                })
                ->when( $request->use_alt_address == 'N', function($query) use($request) {
                    $query->where(function($q) {
                        $q->where('use_alt_address', '0')
                          ->orWhereNull('use_alt_address');
                    });
                })
                ->orderBy('charity_name');

        return $sql;
    }


}
