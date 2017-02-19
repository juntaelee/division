<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class LdapController extends Controller
{
    public function __construct()
    {
        //
    }

    public function ldap(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');

        if ($this->auth($id, $password)) {
            return redirect()->route('main');
        } else {
            if ($user = $this->checkLdap()) {
                // ldap 계정이 존재하고 패스워드까지 정확하다면
                // 계정 정보를 저장하고( register() ) 로그인 처리 함.

                // return $user;
                // return redirect()->route('main');
            } else {
                $errors = [
                    'id' => trans('auth.failed')
                ];
                return redirect()->back()
                    ->withInpu($request->only($request->input('id'), 'remember'))
                    ->withErrors($errors);
            }
        }
        // Auth::logout();
    }

    private function test()
    {
        return false;
    }

    private function checkLdap()
    {
        return false;
    }

    private function auth($id, $password)
    {
        if (Auth::attempt(['id' => $id, 'password' => $password], true)) {
            return true;
        } else {
            return false;
        }
    }

    private function register($user)
    {
        User::create([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
        ]);
    }
}
