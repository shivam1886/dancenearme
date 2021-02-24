<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('frontend.index');
    }

    public function coachProfile(){
        return view('frontend.coach-profile');
    }

    public function danceCategory(){
        return view('frontend.dance-category');
    }

    public function gigsDetails(){
        return view('frontend.gigs-details');
    }

    public function lessionCost(){
        return view('frontend.lessons-cost');
    }

    public function join(){
        return view('frontend.join');
    }

    public function services(){
        return view('frontend.services');
    }

    public function login(){
        return view('frontend.login');
    }

    public function signup(){
        return view('frontend.signup');
    }

    public function signupStep2(){
        return view('frontend.signup-step2');
    }

    public function signupStep3(){
        return view('frontend.signup-step3');
    }

    public function teacherAccount(){
        return view('frontend.teacher-account');
    }

    public function userProfile(){
        return view('frontend.user-profile');
    }

}
