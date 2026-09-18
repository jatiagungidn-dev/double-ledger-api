<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'example231',
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'status',
            'message',
            'data',
            'token',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jangkung231@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'example321',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jangkung231@example.com',
            'password' => 'example321',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'data',
            'token',
        ]);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'example321',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jangkung231@example.com',
            'password' => 'example231',
        ]);

        $response->assertStatus(422)->assertJson([
            'message' => 'Credentials do not match our records',
            'errors' => [
                'email' => [
                    'Credentials do not match our records',
                ],
            ],
        ]);
    }

    public function test_login_rejects_unknown_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'jangkung321@example.com',
            'password' => 'example231',
        ]);

        $response->assertStatus(422)->assertJson([
            'message' => 'Credentials do not match our records',
            'errors' => [
                'email' => [
                    'Credentials do not match our records',
                ],
            ],
        ]);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $userData = [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'example231',
        ];

        $this->postJson('/api/register', $userData)
            ->assertStatus(201);

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_registration_rejects_short_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => '1234567',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->postJson('/api/register', [
            'email' => 'jangkung231@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'password' => '12345678',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_registration_requires_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_login_requires_email(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'KungJang231',
        ]);

        $response = $this->postJson('/api/login', [
            'password' => 'KungJang231',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_password(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Jangkung',
            'email' => 'jangkung231@example.com',
            'password' => 'KungJang231',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jangkung231',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }
}
