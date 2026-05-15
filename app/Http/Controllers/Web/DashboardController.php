<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $jobs  = $user->imageJobs()->latest()->limit(10)->get();
        $plan  = $user->effectivePlan();
        $today = $user->todayJobsCount();
        return view('dashboard', compact('user', 'jobs', 'plan', 'today'));
    }
}
