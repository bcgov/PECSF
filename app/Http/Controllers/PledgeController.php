<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PledgeController extends Controller
{
    public function download($file) {
        return Storage::disk('report')->download($file);
    }
}
