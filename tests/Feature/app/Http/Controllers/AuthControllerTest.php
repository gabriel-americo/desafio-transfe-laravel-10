<?php

namespace Feature\app\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'novo123',
        ];

        $response = $this->post(route('authenticate', ['provider' => 'deixa-o-sub']), $payload);

        $response->assertStatus(422);
        $response->assertJson(['errors' => ['main' => 'Wrong provider provided']]);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'novo123',
        ];

        $response  = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $response->assertStatus(401);
        $response->assertJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    public function testUserShouldSendWrongPassword()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $request  = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertStatus(401);
        $request->assertJson(['errors' => ['main' => 'Wrong credentials']]);
    }
}
