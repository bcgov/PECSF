<?php

namespace App\Http\Controllers\System;

use DateTime;
use DateTimeZone;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;


class UploadFileController extends Controller
{

    public  $PATH_OPTIONS = [
                 0 => 'app/uploads',
                //  1 => 'img/uploads/_adminer',
            ];

    //
    function __construct()
    {
        $this->middleware(['role:sysadmin']);

    }

    public function index(Request $request) {

        $user = User::where('id', Auth::id() )->first();
        if ($user->source_type != 'HCM') {
            abort(403);
        }

        
        $location = $request->has('location') ? $request->location : 0;
        if (session('location')) {
            $location = session('location');
        }


        switch ($location) {
            case 1:
                // $path = public_path('img/uploads/_adminer');
                $path = public_path ( $this->PATH_OPTIONS[1] );
                break;
            default:
                // $path = storage_path('app/uploads');
                $path = storage_path( $this->PATH_OPTIONS[0] );
        }

        $files = File::files($path);

        foreach($files as $file) {
            

            // $lastmodified = $file->getMTime();
            // $lastmodified = DateTime::createFromFormat("U", $lastmodified);
            // $lastmodified->setTimezone(new DateTimeZone('America/Vancouver'));
            // // dd($lastmodified->format('Y-m-d H:m:s' ));
            // $file->last_modified = $lastmodified;

            $file->last_modified = date('Y-m-d H:i:s', $file->getMTime()) ;            
        }
// dd($files);
//         array_filter($files, function($k) {
//             return $k->isFile() ;
//         });

        
        // dd ($files);

        return view('system-security.upload-files.index', compact('files', 'location') );
    }

    public function store(Request $request)
    {

        $org_filename = $request->file('uploaded_file') ? $request->file('uploaded_file')->getClientOriginalName() : '';

        $validator = Validator::make(request()->all(), [
            'uploaded_file'          => 'required|max:102400|file',
        ],[
            'uploaded_file.required' => 'Please upload an json file', 
            'uploaded_file.mimetypes' => 'The upload file must be a file of type: json, your uploaded file is called ' . $org_filename,
            'uploaded_file.max' => 'Sorry! Maximum allowed size for an image is 100MB, your uploaded file is called ' . $org_filename,
        ]);

       //run validation which will redirect on failure
       $validated = $validator->validate();

       //  if ($validator->fails()) {
       //      return redirect()->route('reporting.donation-upload.index')
       //          ->withErrors($validator)
       //          ->withInput();
       //  }

        $upload_file = $request->file('uploaded_file') ?? null;
        //  $filesize = $upload_file->getSize();
        $original_filename = $upload_file->getClientOriginalName();
        $filename= $original_filename ;

        $path = storage_path(  $this->PATH_OPTIONS[0] );
        switch ($request->location) {
            case 1:
                $path = public_path( $this->PATH_OPTIONS[1] );
                break;
        }

        if( File::exists($path.'/'.$filename)) {
            File::delete($path.'/'.$filename); 
        }            

        $upload_file->move($path,$filename);

        // return response()->json([
        //     'success' => 'File ' . $original_filename . ' was successfully uploaded.'
        // ]);
        // Session::flash('success', 'File ' . $original_filename . ' have been updated successfully' ); 
        // return response()->noContent();
        return redirect('/system/upload-files')
                        ->with('success', 'File with name "' . $original_filename . '" have been updated successfully')
                        ->with('location', $request->location);


        
    }

    public function show(String $location_filename)
    {

        $location = substr($location_filename, 0, 1);
        $filename = substr($location_filename, 2);

        switch ($location) {
            case 1:
                $path = public_path( $this->PATH_OPTIONS[1] );
                break;
            default:
                $path = storage_path( $this->PATH_OPTIONS[0] );
        }

        $file= $path. "/". $filename;

        return Response::download($file, $filename, ['Content-Type: application/text']);                
        
    }

}
