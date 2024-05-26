<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\SessionWithIP;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function checkSignIn(Request $request)
    {
        $ip = $request->ip();
        $data = $request->all();
        $active_session = SessionWithIP::where('ip_address', $ip)->where('is_active', 1)->first();

        if(is_null($active_session))
        {
            $result = self::login($data, $ip);

            if($result == 'success') {
                return response()->json(['status' => 'redirect', 'message' => '/home']);
            }
            else
            {
                return response()->json(['status' => 'error', 'message' => 'Credentials Not Available']);
            }
        }
        else
        {
            return response()->json(['status' => 'success', 'message' => 'Session Exists']);
        }
    }

    public function forceLogin(Request $request)
    {
        $ip = $request->ip();
        $data = $request->all();
        SessionWithIP::where('ip_address', $ip)->update(['is_active' => 0]);

        $result = self::login($data, $ip);

        if($result == 'success') {
            return response()->json(['status' => 'redirect', 'message' => '/home']);
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => 'Credentials Not Available']);
        }
    }

    public static function login($data, $ip)
    {
        if(\Auth::attempt(['email' => $data['username'], 'password' => $data['password']]))
        {
            if(\Auth::check())
            {
                $ses = SessionWithIP::create(['ip_address' => $ip]);
                Session::put('session_id_to_verify', $ses->id);

                return 'success';
            }
            else
            {
                return 'error';
            }
        }
        else
        {
            return 'error';
        }
    }

    public function forceLogout(Request $request)
    {
        \Auth::logout();
        // session::flush();
        $ip = $request->ip();
        SessionWithIP::where('ip_address', $ip)->update(['is_active' => 0]);


        return redirect('login');
    }

    public function checkSession(Request $request)
    {
        $id = Session::get('session_id_to_verify');
        $session = SessionWithIP::find($id);

        if($session->is_active == 0)
        {
            \Auth::logout();
            Session::flush();
            return response()->json(['status' => 'closed', 'message' => '/login']);
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => '/login']);
        }
    }

}
