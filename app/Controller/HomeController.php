<?php

namespace App\Controller;

use App\Models\Note;
use App\Models\User;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('index', [
            'users' => User::get(['id','username', 'email', 'first_name', 'last_name', 'phone_number', 'created_at']),
            'notes' => Note::get(['body', 'user_id']),
            'heading' => 'Dashboard'
        ]);
    }

    public function create()
    {

    }
}