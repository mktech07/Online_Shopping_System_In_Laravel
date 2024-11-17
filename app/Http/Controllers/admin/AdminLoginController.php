<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function authenticate(Request $request){
        //Validating the Email and password
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->passes()){
            //Authenticating the usr
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))){
                $admin = Auth::guard('admin')->user();

                //Checking the role of the user
                if($admin->role == 2){
                    return redirect()->route('admin.dashboard');
                }else{
                    // Doing manual logout the customer and redirect to login page with valid error
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You Not Autherized for this Panel !');
                }

                
            }else{
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is incorrect ');
            }

        }else{
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

    }
}
