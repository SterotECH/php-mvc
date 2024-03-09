<?php

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Http\Auth;
use App\Http\Forms\RegistrationForms;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class UserController extends Controller
{
    public function index(): void
    {
        $this->render('users/index', [
            'users' => User::all(),
            'heading' => 'Users'
        ]);
    }

    public function create(): void
    {
        $this->render('users/create', [
            'heading' => 'Create User'
        ]);
    }

    #[NoReturn] public function store(Request $request): void
    {
        $form = new RegistrationForms();
        ;
        if (!$form->validate($request)) {
            $this->render('users/create', [
                'heading' => 'Create User',
                'errors' => $form->errors(),
            ]);
            exit();
        }
        $options = [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
        ];


        $user = User::create([
            'username' => $request->input('username'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'other_name' => $request->input('other_name') ?? null,
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' => password_hash($request->input('password'), PASSWORD_ARGON2I, $options),
        ]);
        if ($user){
            redirect('/users');
        }
    }



    #[NoReturn] public function show(Request $request): void
    {
        $id = $request->params()->id;
        $user = User::getById($id);
        $this->render("users/show", [
            "user" => $user,
            "heading" => "{$user['first_name']} {$user['last_name']}"
        ]);
    }

    public function edit(Request $request): void
    {
        $id = $request->params()->id;
        $user = User::getById($id);

        $this->render('users/edit', [
            'user' => $user,
            "heading" => "{$user['first_name']} {$user['last_name']}"
        ]);
    }

    public function update(Request $request): void
    {
        $errors = [];

        if (!Validator::string($request->input('username'), 1, 16)) {
            $errors['username'] = 'Username must be at least 1 to 16 characters';
        }
        if (!Validator::string($request->input('first_name'), 1, 100)) {
            $errors['first_name'] = 'First name must be at least 1 to 100 charaters ';
        }

        if (!Validator::string($request->input('last_name'), 1, 100)) {
            $errors['last_name'] = 'Last name must be at least 1 to 100 characters.';
        }

        if (!Validator::email($request->input('email'))) {
            $errors['email'] = 'Invalid email format.';
        }

        if (!Validator::string($request->input('password'), 8, 32)) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }

        if (!Validator::phone($request->input('phone_number'))) {
            $errors['phone_number'] = 'Invalid phone number format';
        }

        $user = User::save([
            'id' => $request->input('id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'other_name' => $request->input('other_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'username' => $request->input('username')
        ]);

        $this->render('users/edit', [
            'user' => $user,
            "heading" => "{$user['first_name']} {$user['last_name']}",
            "error" => $errors,
        ]);

    }

    #[NoReturn] public function destroy(Request $request): void
    {

        redirect('/');
    }
}