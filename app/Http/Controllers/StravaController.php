<?php

namespace App\Http\Controllers;

use App\Models\StravaSetting;
use App\Models\Tracklog;
use Strava;
use App\Models\User;
use Illuminate\Http\Request;

class StravaController extends Controller
{
    public function stravaAuth()
    {
        $user = User::find(auth()->user()->id);
        if ($user->is_connect_strava)
        {
            $token = $user->strava->access_token;
            Strava::unauthenticate($token);
            User::where('id', $user->id)->update([
                'strava_setting_id' => null,
                'is_connect_strava' => false,
            ]);
            return redirect('profile');
        } else
        {
            return Strava::authenticate($scope='read_all,profile:read_all,activity:read_all');
        }

    }

    public function getToken(Request $request)
    {
        $code = $request->input('code');
        $token_data = Strava::token($code);


        // Update the users tokens
        $attributes = [
            'access_token' => $token_data->access_token,
            'refresh_token' => $token_data->refresh_token,
            'expires_at' => $token_data->expires_at,
            'user_id' => auth()->user()->id,
        ];

        $stravaSetting = StravaSetting::create($attributes);
        User::where('id', auth()->user()->id)->update([
            'strava_setting_id' => $stravaSetting->id,
            'is_connect_strava' => true,
        ]);

        return redirect('profile');
    }

    private function updateToken() {
        $user = User::find(auth()->user()->id);

        // Check if current token has expired
        if(strtotime(Carbon::now()) > $user->expires_at)
        {
            // Token has expired, generate new tokens using the currently stored user refresh token
            $refresh = Strava::refreshToken($user->refresh_token);

            // Update the users tokens
            User::where('id', $user->id)->update([
                'access_token' => $refresh->access_token,
                'refresh_token' => $refresh->refresh_token
            ]);

        }else{

        }

    }

    public static function getAllActivities($token, $number = 10)
    {
        $user = auth()->user();
        $tracklogs = Strava::activities($token, 1, $number);
        $savedTracklogs = [];
        foreach ($tracklogs as $tracklog)
        {
            $start_date = StravaController::formatStringToTime($tracklog->start_date);
            $start_date_local = StravaController::formatStringToTime($tracklog->start_date_local);
            $attributes = [
                'original_id' => $tracklog->id,
                'user_id' => $user->id,
                'name' => $tracklog->name,
                'distance' => $tracklog->distance,
                'moving_time' => $tracklog->moving_time,
                'elapsed_time' => $tracklog->elapsed_time,
                'start_date' => $start_date,
                'start_date_local' => $start_date_local,
                'timezone' => $tracklog->timezone,
                'average_speed' => $tracklog->average_speed,
                'type' => $tracklog->type,
            ];
            $saved = Tracklog::create($attributes);
            $savedTracklogs[] = $saved;
        }
        return $savedTracklogs;
    }

    public static function getActivity($token, $activityID )
    {
        return Strava::activity($token, $activityID);
    }

    public static function formatStringToTime($str)
    {
        $startTime = strtotime($str);
        return date("Y-m-d H:i:s",$startTime);
    }
}
