<?php

namespace App\Http\Controllers\System;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AnnouncementRequest;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $announcement = Announcement::firstOrNew();

        return view('system-security.announcements.index', compact('announcement'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {

        $announcement = Announcement::first();

        if ($announcement) {

            $announcement->title = $request->title;
            $announcement->status = $request->status;
            $announcement->start_date = $request->start_date;
            $announcement->end_date = $request->end_date;
            $announcement->body = $request->body;

            $announcement->updated_by_id = Auth::id();

            $announcement->save();

        } else {

            $announcement = Announcement::Create([   
                'title' => $request->title,  
                'status' => $request->status, 
                'start_date' => $request->start_date, 
                'end_date' => $request->end_date, 
                'body' => $request->body,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(), 
            ]);

        }

        return redirect()->route('system.announcement.index')
                ->with('success','The announcement was successfully saved.');

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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    
    public function storeImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileName = $request->file('upload')->getClientOriginalName();
            // $fileName = pathinfo($originName, PATHINFO_FILENAME);
            // $extension = $request->file('upload')->getClientOriginalExtension();
            // $fileName = $fileName . '_' . time() . '.' . $extension;
    
            $request->file('upload')->move(public_path('img/uploads/announcement'), $fileName);
    
            $url = asset('img/uploads/announcement/' . $fileName);
            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }
}
