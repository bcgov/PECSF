<?php

namespace App\Http\Controllers\Admin;

use File;
use App\Models\FSPool;

use App\Models\Region;

use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\FSPoolCharity;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class FundSupportedPoolController extends Controller
{

    //
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:setting');

         $this->image_folder = "img/uploads/fspools/";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $pools = FSPool::with('region', 'charities', 'charities.charity')
                    ->when( $request->effectiveTypeFilter == 'F', function ($q)  {
                        return $q->where('start_date', '>', today() );
                    })
                    ->when( $request->effectiveTypeFilter == 'H', function ($q)  {
                        return $q->where('start_date', '<', function ($query) {
                                    $query->selectRaw('max(start_date)')
                                            ->from('f_s_pools as A')
                                            ->whereColumn('A.region_id', 'f_s_pools.region_id')
                                            ->where('A.start_date', '<=', today());
                                    });
                    })
                    ->when( $request->effectiveTypeFilter == 'C', function ($q)  {
                        return $q->where('start_date', '=', function ($query) {
                                    $query->selectRaw('max(start_date)')
                                            ->from('f_s_pools as A')
                                            ->whereColumn('A.region_id', 'f_s_pools.region_id')
                                            ->where('A.start_date', '<=', today());
                                    });
                    })
                    ->select('f_s_pools.*');

            // return ([ $request->effectiveTypeFilter, $pools->toSql(), $pools->getBindings() ]);

            return Datatables::of($pools)
                ->addColumn('action', function ($pool) {
                    // $html = '<a href="'. route('settings.fund-supported-pools.show', $pool->id) .
                    //         '"class="btn btn-info btn-sm  show-pool" data-id="'. $pool->id .'" >Show</a>' ;
                    // if ($pool->canDelete) {
                    //     $html .= '<a href="'. route('settings.fund-supported-pools.edit', $pool->id) .
                    //         '"class="btn btn-primary btn-sm ml-2 edit-pool" data-id="'. $pool->id .'" >Edit</a>';
                    //     // $html .= '<button type="button" class="btn btn-danger btn-sm ml-2 delete-pool" data-toggle="modal" ' .
                    //     //     ' "data-target="#pool-delete-modal" data-id="'. $pool->id .
                    //     //     ' "data-region="' . $pool->region->name . '">Delete</button>';
                    // }
                    // if ($pool->EffectiveType == 'C') {
                    //     $html .= '<a class="btn btn-success btn-sm ml-2 duplicate-pool" data-id="'. $pool->id .
                    //         '" data-region="'. $pool->region->name . '" data-start-date="' . $pool->start_date . '">Duplicate</a>';
                    // }
                    // if ($pool->canDelete) {
                    //     $html .= '<a class="btn btn-danger btn-sm ml-2 delete-pool" data-id="'. $pool->id .
                    //         '" data-region="'. $pool->region->name . '" data-start-date="' . $pool->start_date . '">Delete</a>';
                    // }
                    $html = '<form action="'. route('settings.fund-supported-pools.show', $pool->id) . '" style="display:inline">' .
                                '<input class="btn btn-info btn-sm show-pool" type="submit" value="Show">' .               
                            '</form>';
                    if ($pool->canDelete) {
                        $html .= '<form action="'. route('settings.fund-supported-pools.edit', $pool->id) . '" style="display:inline">' .
                                    '<input class="btn  btn-primary btn-sm ml-2 edit-pool" type="submit" value="Edit">' .               
                                  '</form>';
                    }
                    if ($pool->EffectiveType == 'C') {
                        $html .= '<button class="btn btn-success btn-sm ml-2 duplicate-pool" data-id="'. $pool->id .
                            '" data-region="'. $pool->region->name . '" data-start-date="' . $pool->start_date . '">Duplicate</button>';
                    }
                    if ($pool->canDelete) {
                        $html .= '<button class="btn btn-danger btn-sm ml-2 delete-pool" data-id="'. $pool->id .
                            '" data-region="'. $pool->region->name . '" data-start-date="' . $pool->start_date . '">Delete</button>';
                    }
                    return $html;
            })
            ->rawColumns(['action','charities'])
            ->make(true);
        }

        return view('admin-campaign.fund-supported-pools.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $errors = session('errors');
        if ($errors) {
            $old = session()->getOldInput();
            // dd( $old );

            if (isset($old['charities'])) {
                foreach ($old['charities'] as $key => $charity_id ) {
                    $charity = $charity_id ? Charity::where('id', $charity_id)->select('id', 'charity_name', 'registration_number')->first() : null;
                    $request->session()->flash('charity'.$key.'_selected', $charity);
                }
            }
        }

        $regions = Region::where('status', 'A')->get();


        return view('admin-campaign.fund-supported-pools.create', compact('regions'));

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
        $validator = Validator::make(request()->all(), [
            'region_id'         => 'required',
            'start_date'        => ['required',
                Rule::unique('f_s_pools')->where(function ($query) use($request) {
                    return $query->where('region_id', $request->input('region_id'))
                                 ->where('start_date', $request->input('start_date'))
                                 ->whereNull('deleted_at');;
                })
            ],
            'charities.*'       => ['required'],
            'status.*'          => ['required', Rule::in(['A', 'I'])],
            'names.*'           => 'required|max:50',
            'descriptions.*'    => 'required',
            'percentages.*'     => 'required|numeric|min:0.01|max:100|between:0,100.00|regex:/^\d+(\.\d{1,2})?$/',
            'contact_names.*'   => 'required',
            'contact_emails.*'  => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'images.*'          => 'required|mimes:jpg,jpeg,png,bmp|max:2048',
        ],[
            'start_date.required' => 'The date field is incomplete or has an invalid date.',
            'charities.*.required' => 'The charity field is required.',
            'status.*.in' => 'The selected status is invalid.',
            'names.*.required' => 'Please enter a supported program name.',
            'descriptions.*.required' => 'Please enter a supported program description.',
            'percentages.*.required' => 'Please enter a percentage.',
            'percentages.*.max' => 'The percentage must not be greater than 100.',
            'percentages.*.min' => 'The percentages must be at least 0.01',
            'percentages.*.numeric' => 'The percentage must be a number.',
            'percentages.*.between' => 'The percentage must be between 0 and 100.',
            'percentages.*.regex' => 'The percentage format is invalid.',
            'contact_names.*.required' => 'Please enter a contact name.',
            'contact_titles.*.required' => 'Please enter a contact title',
            'contact_emails.*.required' => 'Please enter a valid email format.',
            'contact_emails.*.email' => 'Please enter a valid email format.',
            'contact_emails.*.regex' => 'Please enter a valid email format.',
            'notes.*.required' => 'The Notes field is required.',
            'images.*.required' => 'Please upload an image',
            'images.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
            'images.*.mimes' => 'The image must be a file of type: jpg, jpeg, png, bmp.',
            
        ]);

        //hook to add additional rules by calling the ->after method
        $validator->after(function ($validator) use($request) {

            $charities = request('charities');
            $status = request('status');
            $percentages = request('percentages');


            $status = request('status');
            if ($charities) {

                // Not sure whats happening here you can only put one charity and one percentage why the loop?
                $sum = 0;
                for ($i=0; $i < count($charities); $i++) {
                    if ($status[$i] == 'A' && is_numeric($percentages[$i]) ) {
                        $sum += $percentages[$i];
                    }
                }

             //   $sum = $percentages;

                if ( round($sum,2) != 100) {
                    for ($i=0; $i < count($charities); $i++) {
                        if ($status[$i] == 'A') {
                            $validator->errors()->add('percentages.' .$i, 'The sum of percentage is not 100%.');
                        }
                    }
                }

                // Check duplicate charity id
                $dups = array_count_values(
                    array_filter($charities, fn($value) => !is_null($value) && $value !== '')
                );

                for ($i=0; $i < count($charities); $i++) {
                    if ( array_key_exists($charities[$i],$dups) && $dups[ $charities[$i] ] > 1) {
                        $validator->errors()->add('charities.' .$i, 'The duplicated charity is entered.');
                    }
                }

                // file name if exists
                $upload_images = $request->file('images') ? $request->file('images') : [];
                for ($i=0; $i < count($charities); $i++) {
                    if (array_key_exists($i, $upload_images)) {
                        $filename= $upload_images[$i]->getClientOriginalName();
                        $extension = $upload_images[$i]->getClientOriginalExtension();
                        if ( strlen($filename) > 50 ) {
                            $validator->errors()->add('images.' .$i, 'The file name ' .$filename . ' is invalid .');
                        }

                        if(!in_array(strtolower($extension),["png","jpg","jpeg","bmp"]))
                        {
                            $validator->errors()->add('images.' .$i, 'The file type ' .$extension . ' is invalid .');
                        }

                    } else {
                        $validator->errors()->add('images.' .$i, 'The image file is required.');
                    }
                }
            } else {
                $validator->errors()->add('region_id', 'You must include at least one Charity.');
            }
        });

        //run validation which will redirect on failure
        $validator->validate();

        // Retrieve the validated input...
        //$validated = $validator->validated();

        $pool = FSPool::Create(
            [
                'region_id' => $request->region_id,
                'start_date' =>  $request->start_date,
                'status' =>  $request->pool_status,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]
        );

        $charities = $request->input('charities', []);
        $percentages = $request->input('percentages', []);
        $status = $request->input('status', []);
        $names = $request->input('names', []);
        $descriptions = $request->input('descriptions', []);
        $contact_titles = $request->input('contact_titles', []);
        $contact_names = $request->input('contact_names', []);
        $contact_emails = $request->input('contact_emails', []);
        $notes = $request->input('notes', []);
        $upload_images = $request->file('images') ? $request->file('images') : [];

        if ($charities) {
            for ($i=0; $i < count($charities); $i++) {
                if ($charities[$i] != '') {

                    // file handling
                    if (array_key_exists($i, $upload_images)) {
                        // dd ( $request->file('images') );
                        $file= $upload_images[$i];
                        $filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );

                        $file->move(public_path( $this->image_folder ), $filename);
                    }

                    $pool->charities()->create([
                        'charity_id'    => $charities[$i],
                        'percentage'    => $percentages[$i],
                        'status'        => $status[$i],
                        'name'          => array_key_exists($i, $names) ? $names[$i] : null,
                        'description'   => array_key_exists($i, $descriptions) ? $descriptions[$i] : null,
                        'contact_title' => array_key_exists($i, $contact_titles) ? $contact_titles[$i] : null,
                        'contact_name'  => array_key_exists($i, $contact_names) ? $contact_names[$i] : null,
                        'contact_email' => array_key_exists($i, $contact_emails) ? $contact_emails[$i] : null,
                        'notes'         => array_key_exists($i, $notes) ? $notes[$i] :null,
                        'image'         => $filename,
                    ]);
                }
            }
        }

        $region = Region::where('id', $request->region_id)->first();

        echo  json_encode(array(route('settings.fund-supported-pools.index')));


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
        $pool = FSPool::where('id', $id)->first();

        return view('admin-campaign.fund-supported-pools.show', compact('pool'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        //
        //
        $pool = FSPool::where('id', $id)->first();

        $errors = session('errors');
        if ($errors) {
            $old = session()->getOldInput();
            // dd( $old );

            if (isset($old['charities'])) {
                foreach ($old['charities'] as $key => $charity_id ) {
                    $charity = $charity_id ? Charity::where('id', $charity_id)->select('id', 'charity_name', 'registration_number')->first() : null;
                    $request->session()->flash('charity'.$key.'_selected', $charity);
                }
            }
        } else {

            foreach ($pool->charities as $key => $pool_charity ) {
                $charity = $pool_charity->charity_id ? Charity::where('id', $pool_charity->charity_id)->select('id', 'charity_name', 'registration_number')->first() : null;
                $request->session()->flash('charity'.$key.'_selected', $charity);
            }
        }



        // show the view and pass the campaign year to it
        return view('admin-campaign.fund-supported-pools.edit', compact('pool'));

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

        // dd($request);
        //
        $validator = Validator::make(request()->all(), [
            'region_id'         => 'required',
            'start_date'        => ['required','date', 'after:today',
                Rule::unique('f_s_pools')->where(function ($query) use($request) {
                    return $query->where('region_id', $request->input('region_id'))
                                    ->where('start_date', $request->input('start_date'))
                                    ->whereNull('deleted_at');;
                })->ignore($id),
            ],
            'pool_status'       => ['required', Rule::in(['A', 'I']) ],

            'charities.*'       => ['required'],
            'status.*'          => ['required', Rule::in(['A', 'I'])],
            'names.*'           => 'required|max:50',
            'descriptions.*'    => 'required',
            'percentages.*'     => 'required|numeric|min:0.01|max:100|between:0,100.00|regex:/^\d+(\.\d{1,2})?$/',
            'contact_names.*'   => 'required',
            'contact_titles.*'  => 'nullable',
            'contact_emails.*'  => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'notes.*'           => 'nullable',
            'images.*'          => 'required|mimes:jpg,jpeg,png,bmp.gif|max:2048',
        ],[
            'start_date.required' => 'The date field is incomplete or has an invalid date.',
            'charities.*.required' => 'The charity field is required.',
            'status.*.in' => 'The selected status is invalid.',
            'names.*.required' => 'Please enter a supported program name.',
            'descriptions.*.required' => 'Please enter a supported program description.',
            'percentages.*.required' => 'Please enter a percentage.',
            'percentages.*.max' => 'The percentage must not be greater than 100.',
            'percentages.*.min' => 'The percentages must be at least 0.01',
            'percentages.*.numeric' => 'The percentage must be a number.',
            'percentages.*.between' => 'The percentage must be between 0 and 100.',
            'percentages.*.regex' => 'The percentage format is invalid.',
            'contact_names.*.required' => 'Please enter a contact name.',
            'contact_titles.*.required' => 'Please enter a contact title',
            'contact_emails.*.required' => 'Please enter a valid email format.',
            'contact_emails.*.email' => 'Please enter a valid email format.',
            'contact_emails.*.regex' => 'Please enter a valid email format.',
            'notes.*.required' => 'The Notes field is required.',
            'images.*.required' => 'Please upload an image',
            'images.*.mimes' => 'Only jpg, jpeg, png and bmp images are allowed',
            'images.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
        ]);

        //hook to add additional rules by calling the ->after method
        $validator->after(function ($validator) use($request) {

            $charities = request('charities');
            $status = request('status');
            $percentages = request('percentages');
            $current_images = request('current_images');

            $status = request('status');
            if ($charities) {

                // Check 100%
                $sum = 0;
                for ($i=0; $i < count($charities); $i++) {
                    if ($status[$i] == 'A' && is_numeric($percentages[$i]) ) {
                        $sum += $percentages[$i];
                    }
                }
                if ( round($sum,2) != 100) {
                    for ($i=0; $i < count($charities); $i++) {
                        if ($status[$i] == 'A') {
                            $validator->errors()->add('percentages.' .$i, 'The sum of percentage is not 100.');
                        }
                    }
                }

                // Check duplicate charity id
                $dups = array_count_values(
                    array_filter($charities, fn($value) => !is_null($value) && $value !== '')
                );

                for ($i=0; $i < count($charities); $i++) {
                    if ( array_key_exists($charities[$i],$dups) && $dups[ $charities[$i] ] > 1) {
                        $validator->errors()->add('charities.' .$i, 'The duplicated charity is entered.');
                    }
                }

                // file name if exists
                $upload_images = $request->file('images') ? $request->file('images') : [];
                for ($i=0; $i < count($charities); $i++) {
                    if (array_key_exists($i, $upload_images)) {
                        $filename= $upload_images[$i]->getClientOriginalName();
                        if ( strlen($filename) > 50 ) {
                            $validator->errors()->add('images.' .$i, 'The file name ' . $filename . ' is invalid.');
                        }
                    } else {
                        if (is_null($current_images[$i])) {
                             $validator->errors()->add('images.' .$i, 'The image file is required .');
                        }
                    }
                }
            } else {
                $validator->errors()->add('region_id', 'You must include at least one Charity.');
            }

        });

        //run validation which will redirect on failure
        $validator->validate();

        $charities = $request->input('charities', []);
        $percentages = $request->input('percentages', []);
        $status = $request->input('status', []);
        $names = $request->input('names', []);
        $descriptions = $request->input('descriptions', []);
        $contact_titles = $request->input('contact_titles', []);
        $contact_names = $request->input('contact_names', []);
        $contact_emails = $request->input('contact_emails', []);
        $notes = $request->input('notes', []);
        $upload_images = $request->file('images') ? $request->file('images') : [];


        $pool = FSPool::where('id', $id)->first();

        // Step 1 -- Update or create
        if ($charities) {
            for ($i=0; $i < count($charities); $i++) {
                if ($charities[$i] != '') {

                    $pool_charity = $pool->charities()->updateOrCreate([
                        'charity_id'    => $charities[$i],
                    ],[
                        'percentage'    => $percentages[$i],
                        'status'        => $status[$i],
                        'name'          => array_key_exists($i, $names) ? $names[$i] : null,
                        'description'   => array_key_exists($i, $descriptions) ? $descriptions[$i] : null,
                        'contact_title' => array_key_exists($i, $contact_titles) ? $contact_titles[$i] : null,
                        'contact_name'  => array_key_exists($i, $contact_names) ? $contact_names[$i] : null,
                        'contact_email' => array_key_exists($i, $contact_emails) ? $contact_emails[$i] : null,
                        'notes'         => array_key_exists($i, $notes) ? $notes[$i] :null,
                        // 'image'         => $filename,
                    ]);

                    // copy and delete the file in folder
                    if (array_key_exists($i, $upload_images)) {
                        // dd ( $request->file('images') );
                        $file= $upload_images[$i];
                        $new_filename=date('YmdHis').'_'. str_replace(' ', '_', $file->getClientOriginalName() );
                        $file->move(public_path( $this->image_folder ), $new_filename);

                        // Clean up old file
                        if ($pool_charity->image) {
                            $old_filename = public_path( $this->image_folder ).$pool_charity->image;
                            if (File::exists( $old_filename )) {
                                File::delete( $old_filename );
                            }
                        }
                        // Store the new filename in record
                        $pool_charity->image = $new_filename;
                        $pool_charity->save();
                    }

                }
            }
        }
        // Step 2 -- Delete the row
        $dels = array();
        foreach( $pool->charities as $charity ) {
            if (!in_array($charity->charity_id, $charities)) {
                array_push($dels, $charity->id);

                // delete the image file from image folder
                $filename = public_path( $this->image_folder ) . $charity->image;
                if (File::exists( $filename )) {
                    File::delete( $filename );
                }
            }
        }
// dd([ $charities, $pool->charities->pluck('charity_id'), $dels ]);
        FSPoolCharity::whereIn('id', $dels)->delete();

        // Step 3 -- Update the header
        $pool->start_date = $request->start_date;
        $pool->status = $request->pool_status;
        $pool->updated_by_id = Auth::id();
        $pool->save();


        //$region = Region::where('id', $request->region_id)->first();

        return redirect()->route('settings.fund-supported-pools.index')
            ->with('success','Fund Supported Pool ' . $request->region  .
                ' for start date ' . $request->start_date .
                ' updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        // TODO: check any transactions created for this pool yet based on the start_date

        $pool = FSPool::where('id', $id)->first();

        $validator = Validator::make(request()->all(), [
        ]);

        $validator->after(function ($validator) use($pool) {

            if (!($pool->canDelete)) {
                $validator->errors()->add('region', 'This is not allowed to delete this Fund Supported Pool since the transcations already exists!');
            }
        });

        //run validation which will redirect on failure
        $validator->validate();

        // Delete Process
        foreach ($pool->charities as $pool_charity) {
            // Clean up old file
            if ($pool_charity->image) {
                $old_filename = public_path( $this->image_folder ).$pool_charity->image;
                if (File::exists( $old_filename )) {
                    File::delete( $old_filename );
                }
            }
        }

        // TODO: delete file and subrecord
        $pool->charities()->delete();

        $pool->updated_by_id = Auth::Id();
        $pool->save();
        $pool->delete();

        return response()->noContent();

    }


        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id, Request $request)
    {
        // TODO: check any transactions created for this pool yet based on the start_date
        // dd($request);

        // return  ( $request->new_start_date );

        $pool = FSPool::where('id', $id)->first();

        $validator = Validator::make(request()->all(), [
                'start_date'        => ['required', 'after:yesterday',
                Rule::unique('f_s_pools')->where(function ($query) use($request, $pool) {
                    return $query->where('region_id', $pool->region_id)
                                 ->where('start_date', $request->start_date)
                                 ->whereNull('deleted_at');
                })
            ]
        ],[
            // 'new_start_date.required' => 'The New Start Date field is required.',
            'start_date.required' => 'The date field is incomplete or has an invalid date.',
            'start_date.yesterday' => 'The New Start Date field is required.',
        ]);

        $validator->after(function ($validator) use($pool) {

            // if (!($pool->canDelete)) {
            //     $validator->errors()->add('region', 'This is not allowed to delete this Fund Supported Pool since the transcations already exists!');
            // }
        });

        //run validation which will redirect on failure
        $validator->validate();


        // Duplicate
        $clone = $pool->replicate();
        $clone->start_date = $request->start_date;
        $clone->created_by_id = Auth::id();
        $clone->updated_by_id = Auth::id();
        $clone->push();

        foreach($pool->charities as $charity)
        {
           $old_image = $charity->image;
           $new_image = date('YmdHis'). substr($charity->image, 12);

           $old_filename = public_path( $this->image_folder ).$old_image;
           $new_filename = public_path( $this->image_folder ).$new_image;
           if (File::exists( $old_filename )) {
               File::copy( $old_filename, $new_filename );
           }

           $charity->image = $new_image;
           $clone->charities()->create( $charity->toArray() );

        }

        $clone->save();

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
            $formatted_charities[] = ['id' => $charity->id, 'text' => $charity->charity_name . ' (' . $charity->registration_number . ')'  ];
        }

        return response()->json($formatted_charities);
    }
}
