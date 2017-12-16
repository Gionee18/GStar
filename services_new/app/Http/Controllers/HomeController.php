<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
		if(Auth::user()){
			$result = array('count'=>1,'msg'=>'success', 'data'=>Auth::user());
			return json_encode($result);
		}
		//print_r(Auth::user());
        return view('home');
    }
}
