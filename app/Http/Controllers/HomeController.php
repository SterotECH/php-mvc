<?php

namespace App\Http\Controllers;

use App\Core\Authenticator;
use App\Core\Request;
use App\Core\Session;
use App\Http\Forms\LoginForm;
use App\Http\Forms\RegistrationForms;
use App\Models\Note;
use App\Models\User;
use Random\RandomException;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('index', [
            'users' => User::all(),
            'notes' => Note::all(),
            'heading' => 'Dashboard'
        ]);
    }

    public function render_login(): void
    {
        $this->render('auth/login', [
            'heading' => 'Sign in to your account',
            'errors' => Session::get('errors')
        ]);
    }

    public function render_register(): void
    {
        $this->render('auth/register', [
            'heading' => 'Create new Account',
            'errors' => Session::get('errors')
        ]);
    }

    public function render_forgot_password(): void
    {
        $this->render('auth/forgot_password', [
            'heading' => 'Forgot Password'
        ]);
    }


    /**
     * @throws RandomException
     */
    public function login(Request $request): void
    {
        $this
            ->validate($attributes = [
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ],  [
                'email' => 'required|email',
                'password' => "required|string|password|passwordVerify:{$request->input('email')}",
            ]);


        Authenticator::attempt(
            $attributes['email'],
            $attributes['password']
        );

        redirect('/');
    }

    public function register(Request $request): void
    {
        $this->validate((array)$request->all(), [
            'username' => 'required|string|min:2|max:16|unique:users,username',
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|password',
            'phone_number' => 'required|string|min:10|max:20|unique:users,phone_number|regex:/^([0-9\s\-\+\(\)]*)$/',
            'other_name' => 'string|min:2|max:255',
        ]);

        $user = Authenticator::register((array)$request->all());

        if ($user){
        redirect('/');
        }
    }

    public function logout(): void
    {
        Authenticator::logout();
        redirect('/auth/login');
    }
}
