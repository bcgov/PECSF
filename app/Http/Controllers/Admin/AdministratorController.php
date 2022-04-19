<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use App\MicrosoftGraph\TokenCache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdministratorController extends Controller
{
    //
    function __construct()
    {
         $this->middleware('permission:setting');
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
        //
        $administrators = User::role('admin')->get();

        // load the view and pass the sharks
        return view('admin.administrators.index', compact('administrators'));

    }

    public function store(Request $request)
    {

        // validation
        if (!($request->input('user_id'))) 
        {
            throw ValidationException::withMessages(
                ['user' => 'Please specify a user to be assign to the admin role.']
            );
        }

        //$user = User::find($request->user_id);
        $user = User::where('azure_id',$request->user_id)->first();

        if ($user) {
            if ($user->hasRole('admin'))
            {
                throw ValidationException::withMessages(
                    ['user' => 'User ' . $user->name . ' already has administrator role assigned']
                );
            }
        } else {
            //  create the new user and assign the role when no user found

            $tokenCache = new TokenCache();
            $accessToken = $tokenCache->getAccessToken();

            $graph = new Graph();
            $graph->setAccessToken($accessToken);

            $azure_user = $graph->createRequest('GET', '/users/' . $request->input('user_id') )
                ->setReturnType(Model\User::class)
                ->execute();
            
                if ($azure_user) {
                // read more information from Graph API
                $user = User::create([
                    'name' => $azure_user->getDisplayName(),
                    'email' => $azure_user->getMail(),
                    'azure_id' => $azure_user->getId(),
                    'password' => Hash::make( random_bytes(26) ),
                ]);
            }

        }

        $user->assignRole('admin');
        
        return redirect()->route('administrators.index')
            ->with('success','User ' . $user->name . ' was assigned to Administrator role.');

    }

    public function getUsers(Request $request)
    {
        /*
        $term = trim($request->q);

        if($term == ''){
            $users = User::orderby('name','asc')->select('id','name','email')->limit(50)->get();
         }else{
            $users = user::orderby('name','asc')->select('id','name','email')->where('name', 'like', '%' .$term . '%')->limit(50)->get();
         }
   
         $formatted_users = [];
         foreach ($users as $user) {
            $formatted_users[] = ['id' => $user->id, 'text' => $user->name];
        }

        return response()->json($formatted_users);
        */

        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();
    
        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $queryParams = array(
           '$select' => 'id,displayName,mail,userPrincipalName',
           //'$filter'  =>  "startswith(displayName,'". $request->q . "')",
           //'$search'  =>  '"displayName:' . $request->q . '"',
           '$orderby' => 'displayName'
         );
     
       if (trim($request->q)) {
           $queryParams['$search'] = '"displayName:' . trim($request->q) . '"';
       }
  
        // test User  API https://graph.microsoft.com/v1.0/users
       $getUsersUrl = '/users?'.http_build_query($queryParams);
       $users = $graph->createRequest('GET', $getUsersUrl)
              ->addHeaders(['ConsistencyLevel'=> 'eventual'])
               ->setReturnType(Model\User::class)
               ->execute();

        $formatted_users = [];
        foreach ($users as $user) {
             $formatted_users[] = [
                    'id' => $user->getId(), 
                    'text' => $user->getDisplayName() . ' (' . $user->getMail() . ')'
            ];
        }

        // return "[{'id':31,'name':'Abc'}, {'id':32,'name':'Abc12'}, {'id':33,'name':'Abc123'},{'id':34,'name':'Abc'}]";
        return response()->json($formatted_users);

    }
    
    public function destroy($id)
    {
         
        $user = User::find($id);

        // checking whether this is the last one.
        // $administrators = User::role('admin')->pluck('id')->toArray();
        $administrators = User::role('admin')->get();
        
        if (count($administrators) == 1 and $administrators->contains($user))
        {
            throw ValidationException::withMessages(
                ['user' => 'At least one administrator must exist.']
            );
        }

        if ( $id == Auth::id() ) 
        {
            throw ValidationException::withMessages(
                ['user' => 'You cannot remove administrator role by yourself.']
            );
        }

        $user->removeRole('admin');

        return redirect()->route('administrators.index')
          ->with('success','User ' . $user->name . '  was removed from Administrator role.');

    }

}
