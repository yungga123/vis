<?php

namespace App\Validation;

class Accounts
{
    // public function custom_rule(): bool
    // {
    //     return true;
    // }
    public $sign_in = [
        'username' => 'required|max_length[50]',
        'password' => 'required|max_length[50]',
    ];

    public $sign_in_errors = [
        'username' => [
            'required' => 'Please enter a username.',
            'max_length' => 'Username is limited to 50 characters.'
        ],
        'password' => [
            'required' => 'Please enter a password.',
            'max_length' => 'Password is limited to 50 characters.'
        ]
    ];
}
