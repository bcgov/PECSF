<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogViewerController extends Controller
{
    //
    //
    function __construct()
    {
        //  $this->middleware('permission:setting');
        $this->middleware(['role:sysadmin']);
        
    }

    public function index(Request $request) {

        $user = User::where('id', Auth::id() )->first();
        if ($user->source_type != 'HCM') {
            abort(403);
        }

        /*  Symfony\Component\Finder\SplFileInfo

            filename 
            size
            mTime
            cTime
            type
            extension
        */
 
        if($request->ajax()) {

            $path = storage_path('logs');
            $files = File::files($path);

            $collection = collect([]);

            foreach($files as $file) {

                $file->last_modified = date('Y-m-d H:i:s', $file->getMTime()) ;   
                $collection->push(
                    [ 'filename' => $file->getFilename(),
                    'type' => $file->getType(),
                    'size' => number_format(round($file->getSize() / 1024,0),0),
                    'last_modified' => $file->last_modified,
                    ]
                );
            }


            return Datatables::of($collection)
            //     ->addColumn('action', function ($business_unit) {
            //     return '<a class="btn btn-info btn-sm  show-bu" data-id="'. $business_unit->id .'" >Show</a>' .
            //            '<a class="btn btn-primary btn-sm ml-2 edit-bu" data-id="'. $business_unit->id .'" >Edit</a>' .
            //            '<a class="btn btn-danger btn-sm ml-2 delete-bu" data-id="'. $business_unit->id .
            //            '" data-code="'. $business_unit->code . '">Delete</a>';
            // })
            // ->rawColumns(['action'])
                ->make(true);
        }


//  dd($files);
//         array_filter($files, function($k) {
//             return $k->isFile() ;
//         });
        // $data= $this->paginate($files);
        // $data->withPath( route('system.log-files.index') );

        // return view('system-security.log-files.index', compact('files', 'data', 'collection') );
        return view('system-security.log-files.index');
    }


    public function show(String $filename)
    {
        $path = storage_path('logs');
        $file= $path. "/". $filename;

        return Response::download($file, $filename, ['Content-Type: application/text']);                
        
    }

    public function phpinfo_page(Request $request)
    {

        $user = User::where('id', Auth::id() )->first();
        if ($user->source_type != 'HCM' && (!(App::environment('local'))) ) {
            abort(403);
        }

        return phpinfo();              
        
    }

}
