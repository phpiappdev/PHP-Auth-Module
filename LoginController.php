<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;
use App\Models\User;
use Carbon\Carbon; 

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){   
   
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ],
        [
            'email.required' => trans('global.email_phone_required')
        ]);

        $remember = $request->has('remember') ? true : false;

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $attemptFields = [$fieldType => $request->email];
        if($fieldType == 'phone'){
            $attemptFields['isd_code'] = $request->isd_code;
        }

        $query = User::where($attemptFields);
        if($query->count() > 0){

            $attemptFields['password'] = $request->password;
            $auth = auth()->attempt($attemptFields, $remember);
            if($auth){
                $userid = auth()->user()->id;
                User::where('id', $userid)->increment('total_login', 1, ['last_login' => Carbon::now(), 'isOnline' => '1']);
                Session::put('loginUserRole', $request->loginUserRole);
                if(auth()->user()->roles->contains('1')){
                    return redirect('admin/home');
                }
                return redirect()->route('dashboard');

            } else {
                return redirect()->back()->with('error', trans('validation.password'));
            }  
        } else {
            return redirect()->back()->with('error', trans('global.email_phone_not_exists'));
        }      
          
    }


    public function logout(Request $request) {
        if(auth()->user()){
            $userid = auth()->user()->id;
            User::where('id', $userid)->update(['isOnline' => '0']);
        }
        Auth::logout();
        return redirect('/');
    }

}
