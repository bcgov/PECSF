<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerRegistrationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;

class VolunteeringController extends Controller
{
    public function index() {
        $organizations = Organization::all();
        $user = User::find(Auth::id());

        return view('volunteering.index', compact('organizations', 'user'));
    }

    public function store(VolunteerRegistrationRequest $request) {
        $input = $request->validated();
        $input['user_id'] = Auth::id();
        Volunteer::create($input);
        return redirect()->route('volunteering.index');
    }
}
