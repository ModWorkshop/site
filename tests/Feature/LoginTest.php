<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login(): void
    {
        $this->post('login', [
            'email' => 'email@modworkshop.net',
            'password' => '123456',
            'remember' => false
        ])->assertStatus(200);
    }

    public function test_login_wrong_password(): void
    {
        $this->post('login', [
            'email' => 'email@modworkshop.net',
            'password' => '1234567',
            'remember' => false
        ])->assertStatus(401);
    }
}
