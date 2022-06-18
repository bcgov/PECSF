<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerRegistrationRequest;
use App\Models\FSPool;
use App\Models\Organization;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;


use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use App\MicrosoftGraph\TokenCache;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\Datatables\Datatables;

class VolunteeringController extends Controller
{
    public function index() {
        $organizations = Organization::all();
        $user = User::find(Auth::id());
        $totalPledgedDataTillNow = Pledge::where('user_id', Auth::id())->sum('goal_amount');
        return view('volunteering.index', compact('organizations', 'user', 'totalPledgedDataTillNow'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $input = $request->validated();
        $input['user_id'] = Auth::id();
        Volunteer::create($input);
        return redirect()->route('volunteering.index');
    }

    public function bank_deposit_form(Request $request) {
        $pools = FSPool::where('start_date', '=', function ($query) {
            $query->selectRaw('max(start_date)')
                ->from('f_s_pools as A')
                ->whereColumn('A.region_id', 'f_s_pools.region_id')
                ->where('A.start_date', '<=', today());
        })
            ->where('status', 'A')
            ->get();
        $regional_pool_id = $pools->count() > 0 ? $pools->first()->id : null;

        return view('volunteering.forms',compact('pools','regional_pool_id'));
    }
}
