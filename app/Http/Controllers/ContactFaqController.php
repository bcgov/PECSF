<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactFaqController extends Controller
{
    public function index() {
        $sections = [
            'Donations',
            'Volunteering',
            'Calendar'
        ];
        return view('contact.index', compact('sections'));
    }
}
