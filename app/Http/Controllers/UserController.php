<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Strava;

class UserController extends Controller
{
    //
    public function profile()
    {
        $user = auth()->user();
        $athlete = Strava::athlete($user->strava->access_token);
        return $this->formatJSON($athlete);
    }

    public function getLatestActivities($number = 10)
    {
        $user = auth()->user();
        $activities = StravaController::getAllActivities($user->strava->access_token, $number);
        return $this->formatJSON($activities);
    }

    public function activity($id)
    {
        $user = auth()->user();
        $activity = StravaController::getActivity($user->access_token, $id);
        return $this->formatJSON($activity);
    }


    private function formatJSON($data) {
        return response()->json($data);
    }
}
