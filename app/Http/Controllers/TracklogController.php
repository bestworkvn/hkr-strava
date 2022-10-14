<?php

namespace App\Http\Controllers;

use App\Models\Tracklog;
use Illuminate\Http\Request;

class TracklogController extends Controller
{
    public function index()
    {
        return view('sessions.tracklogs', [
            'tracklogs' => Tracklog::latest()->paginate(5)->withQueryString()
        ]);
    }

    public function show(Tracklog $tracklog)
    {
        return view('sessions.tracklog', [
            'tracklog' => $tracklog
        ]);
    }
}
