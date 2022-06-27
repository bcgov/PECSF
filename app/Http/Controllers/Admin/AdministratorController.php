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
use Yajra\Datatables\Datatables;

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
        if($request->ajax()) {

            $administrators = User::role('admin')->get();
        
            return Datatables::of($administrators)
                ->addColumn('rolename', function ($administrator) {
                    return $administrator->getRoleNames()->contains('admin') ? 'admin' : '' ;    
                })
                ->addColumn('action', function ($administrator) {
                    return '<a class="btn btn-danger btn-sm ml-2 delete-administrator" data-id="'. $administrator->id .
                    '" data-name="'. $administrator->name . '"><i class="fa fa-trash"></a>';
                    // return '<a class="btn btn-danger" onclick="return confirm(\'Are you sure to remove user' . $administrator->name . '? \')" href="/settings/administrators/'. $administrator->id . '/delete"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);
        }

        // load the view and pass the sharks
        return view('admin-campaign.administrators.index');
        
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

        $user = User::find($request->user_id);

        if ($user) {
            if ($user->hasRole('admin'))
            {
                throw ValidationException::withMessages(
                    ['user' => 'User ' . $user->name . ' already has administrator role assigned']
                );
            }
        } else {

            throw ValidationException::withMessages(
                ['user' => 'User ' . $user->name . ' not found in the system']
            );

        }

        $user->assignRole('admin');
        
        return redirect()->route('settings.administrators.index')
            ->with('success','User ' . $user->name . ' was assigned to Administrator role.');

    }

    public function getUsers(Request $request)
    {

        $term = trim($request->q);

        if($term == ''){
            $users = User::orderby('name','asc')->select('id','name','email','emplid')->limit(50)->get();
         }else{
            $users = user::orderby('name','asc')
                ->select('id','name','email','emplid')
                ->where( function($query) use($term) {
                     return $query->where('name', 'like', '%' .$term . '%')
                            ->orWhere('emplid', 'like', '%' .$term . '%');
                })
                ->limit(50)->get();
         }
   
         $formatted_users = [];
         foreach ($users as $user) {
            $text = $user->name;
            $text .= $user->emplid ? ' (' . $user->emplid . ')' : '';
            $formatted_users[] = ['id' => $user->id, 'text' => $text];
        }

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

        return redirect()->route('settings.administrators.index')
          ->with('success','User ' . $user->name . '  was removed from Administrator role.');

    }

}
