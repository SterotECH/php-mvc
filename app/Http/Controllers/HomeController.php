<?php

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Http\Auth;
use App\Http\Forms\LoginForm;
use App\Models\Note;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

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

    public function render_login(Request $request): void
    {
        $this->render('auth/login', [
            'heading' => 'Sign in to your account'
        ]);
    }

    public function render_register(Request $request): void
    {
        $this->render('auth/register', [
            'heading' => 'Create new Account'
        ]);
    }

    public function render_forgot_password(Request $request): void
    {
        $this->render('auth/forgot_password', [
            'heading' => 'Forgot Password'
        ]);
    }

    public function login(Request $request): void
    {
        $form = new LoginForm();
        if (! $form->validate($request)) {
            $this->render('auth/login', [
                'heading' => 'Sign in to your account',
                'errors' => $form->errors()
            ]);
        }
        $user = User::where(['email' => $request->input('email')]);
        if ($user) {
            if (password_verify($request->input('password'), $user[0]->password)) {
                Auth::login($user);
                redirect('/');
            }
        }

        $this->render('auth/login', [
            'heading' => 'Sign in to your account',
            'errors' => [
                'email' => 'No matching account found for that email address and password'
            ]
        ]);


    }


    #[NoReturn] public function logout(Request $request): void
    {
        Auth::logout();
        redirect('/auth/login');
    }
}