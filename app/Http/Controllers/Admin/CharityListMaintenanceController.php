<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MicrosoftGraph\TokenCache;
use App\Models\CompletedJobs;
use App\Models\FailedJobs;
use App\Models\FSPool;
use App\Models\User;
use App\Models\Charity;
use App\Models\Jobs;
use App\Jobs\ProcessCharityList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Microsoft\Graph\Graph;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class CharityListMaintenanceController extends Controller
{

    //
    function __construct()
    {
        $this->middleware('permission:setting');
        $this->charity_file_folder = "charities/uploads/import";
    }

    public function dashboard()
    {
        return view('admin.dashboard.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->ajax()) {
        return response()->view('admin-campaign.charity-list-maintenance.table',compact('charities'));
        }

        $jobs = count(Jobs::all()) > 0 ? Jobs::all() : [];
        $completed_jobs = CompletedJobs::where('payload','like', '%commandName%ProcessCharityList%')
                                ->orderBy('created_at', 'desc')->get();
        $failed_jobs = FailedJobs::where('payload','like', '%commandName%ProcessCharityList%')
                                ->orderBy('failed_at', 'desc')->get();

        // load the view and pass the sharks
        return view('admin-campaign.charity-list-maintenance.index',compact('jobs','failed_jobs','completed_jobs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'charity_list'          => 'required|mimes:txt|max:512000',
        ],[
            'images.required' => 'Please upload an xls xlsx Excel File',
            'images.max' => 'Sorry! Maximum allowed size for an image is 50MB',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.charity-list-maintenance.index')
                ->withErrors($validator)
                ->withInput();
        }

        $upload_file = $request->file('charity_list') ? $request->file('charity_list') : [];
        $filesize = $upload_file->getSize();
        $filename=date('YmdHis').'_'. str_replace(' ', '_', $upload_file->getClientOriginalName() );
        $upload_file->move(storage_path( $this->charity_file_folder ), $filename);
        ProcessCharityList::dispatch(storage_path( $this->charity_file_folder)."/".$filename,$filename,$filesize);

        return redirect()->route('settings.charity-list-maintenance.index')
            ->with('success','File ' . storage_path( $this->charity_file_folder).$filename . ' was assigned added to the process queue.');

    }

    public function getUsers(Request $request)
    {



        $formatted_users = [];


        // return "[{'id':31,'name':'Abc'}, {'id':32,'name':'Abc12'}, {'id':33,'name':'Abc123'},{'id':34,'name':'Abc'}]";
        return response()->json($formatted_users);

    }

    public function getUserFromLocalDatabase(Request $request) {



        return response()->json($formatted_users);

    }

    public function destroy($id)
    {



        return redirect()->route('settings.administrators.index')
            ->with('success','User ' . $user->name . '  was removed from Administrator role.');

    }

}
