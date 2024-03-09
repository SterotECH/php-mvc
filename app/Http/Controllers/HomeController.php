<?php

namespace App\Http\Controllers;

use App\Core\Authenticator;
use App\Core\Request;
use App\Core\Session;
use App\Http\Forms\LoginForm;
use App\Http\Forms\RegistrationForms;
use App\Models\Note;
use App\Models\User;

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
            'errors' =>Session::get('errors')
        ]);
    }

    public function render_forgot_password(): void
    {
        $this->render('auth/forgot_password', [
            'heading' => 'Forgot Password'
        ]);
    }


    public function login(Request $request): void
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];

        $this
            ->validate($attributes = [
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ], $rules);

        $signedIn = Authenticator::attempt(
            $attributes['email'],
            $attributes['password']
        );

        if (!$signedIn) {
            $this
                ->error(
                    'email',
                    'No matching account found for that email address and password'
                )
                ->throwValidationException();
        }

        redirect('/');
    }

    public function register(Request $request): void
    {
        $username = $request->input('username');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');
        $phone_number = $request->input('phone_number');
        $other_name = $request->input('other_name');

        $rules = [
            'username' => 'required|string|min:2|max:16|unique:users,username',
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:16',
            'password_confirmation' => 'same:password',
            'phone_number' => 'required|string|min:11|max:20|unique:users,phone_number',
            'other_name' => 'string|min:2|max:255',
        ];

        $this->validate((array)$request->all(), $rules);

        $user = User::create([
            'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'other_name' => $other_name ?? null,
            'phone_number' => $phone_number,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_ARGON2I, [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
            ]),
        ]);
        Authenticator::login($user);
        redirect('/');
    }

    public function logout(Request $request): void
    {
        Authenticator::logout();
        redirect('/auth/login');
    }
}
