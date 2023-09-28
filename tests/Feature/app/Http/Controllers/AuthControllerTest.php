<?php

namespace Feature\app\Http\Controllers;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        $response = $this->post(route('authenticate', ['provider' => 'deixa-o-sub']));

        $response->assertStatus(422);
        $response->assertJson(['errors' => ['main' => ['Wrong provider provided']]]);
    }
}
