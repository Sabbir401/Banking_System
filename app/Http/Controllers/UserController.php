<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index()
    {

        return view('createUser');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'c_password' => 'required|same:password',
            ]
        );
        try {
            DB::beginTransaction();

        $user = new User();
        $user->name = $request->input('name');
        $user->account_type = $request->input('acc_type');
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->balance = 0;
        $user->save();

            DB::commit();
            return redirect('login')->with('success', 'User information inserted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            // return redirect('register')->with('error', 'An error occurred while inserting user information.');
        }
    }


    public function login()
    {
        return view('login');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/login');

    }


    public function checklogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            Session::put('user_email', $request->email);
            return redirect('transaction');

        } else {
            return response(['email' => 'Invalid credentials']); 
        }
    }


}
