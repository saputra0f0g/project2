<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('peran');
            $table->string('status_akun');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_invalid_login_credentials_return_the_login_error_not_an_expired_session(): void
    {
        User::query()->create([
            'nama_lengkap' => 'Masyarakat',
            'username' => 'masyarakat-1',
            'email' => 'masyarakat@example.test',
            'password' => Hash::make('correct-password'),
            'peran' => 'masyarakat',
            'status_akun' => 'aktif',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'masyarakat@example.test',
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJson(['message' => 'ID atau kata sandi salah.'])
            ->assertJsonMissing(['message' => 'Sesi berakhir. Silakan login kembali.']);
    }

    public function test_password_reset_api_endpoints_are_removed(): void
    {
        $this->postJson('/api/auth/forgot-password')->assertMethodNotAllowed();
        $this->postJson('/api/auth/verify-otp')->assertMethodNotAllowed();
        $this->postJson('/api/auth/reset-password')->assertMethodNotAllowed();
    }
}
