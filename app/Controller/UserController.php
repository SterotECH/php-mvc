<?php

namespace App\Controller;

use App\Core\Request;
use App\Core\Validator;
use App\Models\Note;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class UserController extends Controller
{
    public function index(): void
    {
        $notes = User::get(['username', 'email']);
        $users = Note::get(['body', 'user_id']);

        $this->render('users/index', ['notes' => $notes, 'users' => $users]);
    }

    public function create(): void
    {
        $this->render('users/create', [], 'base');
    }

    #[NoReturn] public function store(Request $request): void
    {
        $validationRules = [
            'username' => 'required|string|min:1|max:255',
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|string|min:1|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|min:1|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
        $validator = new Validator();
        if (!$validator->validate($_POST, $validationRules)) {
            header('Location: /user/create');
            exit;
        }
        $options = [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
        ];

        User::create([
            'username'=>$request->input('username'),
            'first_name'=>$request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'other_name' => $request->input('other_name') ?? null,
            'phone_number' => $request->input('phone_number'),
            'email' => $request->input('email'),
            'password' =>password_hash($request->input('password'), PASSWORD_ARGON2I, $options),
        ]);

        // Redirect to a success page
        header('Location: /');
        exit;
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

        dd($user);

        $this->render('users/edit', [
            'user' => $user,
            "heading" => "{$user['first_name']} {$user['last_name']}"
        ]);
    }

    public function update(Request $request)
    {
        // Validate and update the note in the database
        // Redirect back to the index page
    }

    public function destroy($id)
    {
        header('Location: /');
    }
}