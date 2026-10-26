<?php

namespace Tests\Feature;

use App\Models\LaporanKeluhan;
use App\Models\User;
use App\Notifications\LaporanBaruNotification;
use App\Services\WebPushNotifier;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PushSubscriptionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['webpush.database_connection' => 'sqlite']);

        Schema::dropIfExists('push_subscriptions');
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

        Schema::create('push_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->morphs('subscribable');
            $table->string('endpoint', 1024)->unique();
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });

        Schema::create('laporan_keluhan', function (Blueprint $table): void {
            $table->id();
            $table->string('id_laporan')->nullable();
            $table->foreignId('id_pelapor')->nullable();
            $table->string('kategori_bidang')->default('Jalan');
            $table->string('judul_laporan');
            $table->string('jenis_kerusakan')->nullable();
            $table->string('tingkat_kerusakan')->nullable();
            $table->text('deskripsi_laporan')->nullable();
            $table->string('lokasi_gps')->nullable();
            $table->string('alamat_map')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->json('fotos_bukti')->nullable();
            $table->json('foto_penanganan')->nullable();
            $table->string('status')->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('laporan_keluhan');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_admin_can_save_a_push_subscription(): void
    {
        $admin = $this->createUser('admin_jalan');

        $this->actingAs($admin)
            ->postJson(route('admin_jalan.push_subscriptions.store'), [
                'endpoint' => 'https://push.example.test/subscription/123',
                'keys' => [
                    'p256dh' => 'public-key',
                    'auth' => 'auth-token',
                ],
            ])
            ->assertOk()
            ->assertJson(['message' => 'Notifikasi push berhasil diaktifkan.']);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $admin->id,
            'subscribable_type' => $admin->getMorphClass(),
            'endpoint' => 'https://push.example.test/subscription/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
        ]);
    }

    public function test_admin_cannot_delete_another_admins_subscription(): void
    {
        $admin = $this->createUser('admin_jalan');
        $otherAdmin = $this->createUser('admin_jalan');
        $otherAdmin->updatePushSubscription(
            'https://push.example.test/subscription/other',
            'public-key',
            'auth-token'
        );

        $this->actingAs($admin)
            ->deleteJson(route('admin_jalan.push_subscriptions.destroy'), [
                'endpoint' => 'https://push.example.test/subscription/other',
            ])
            ->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $otherAdmin->id,
            'endpoint' => 'https://push.example.test/subscription/other',
        ]);
    }

    public function test_subscription_endpoint_must_use_https(): void
    {
        $admin = $this->createUser('admin_jalan');

        $this->actingAs($admin)
            ->postJson(route('admin_jalan.push_subscriptions.store'), [
                'endpoint' => 'http://push.example.test/subscription/123',
                'keys' => [
                    'p256dh' => 'public-key',
                    'auth' => 'auth-token',
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('endpoint');
    }

    public function test_new_reports_are_sent_to_active_admins_with_subscriptions(): void
    {
        Notification::fake();
        config([
            'webpush.vapid.public_key' => 'public-key',
            'webpush.vapid.private_key' => 'private-key',
        ]);

        $admin = $this->createUser('admin_jalan');
        $admin->updatePushSubscription(
            'https://push.example.test/subscription/admin',
            'public-key',
            'auth-token'
        );

        $inactiveAdmin = $this->createUser('admin_jalan');
        $inactiveAdmin->status_akun = 'non-aktif';
        $inactiveAdmin->save();
        $inactiveAdmin->updatePushSubscription(
            'https://push.example.test/subscription/inactive',
            'public-key',
            'auth-token'
        );

        $laporan = LaporanKeluhan::query()->create(['judul_laporan' => 'Jalan berlubang']);

        app(WebPushNotifier::class)->notifyAdminsOfNewReport($laporan);

        Notification::assertSentTo($admin, LaporanBaruNotification::class);
        Notification::assertNotSentTo($inactiveAdmin, LaporanBaruNotification::class);
    }

    public function test_mobile_report_creation_triggers_web_push_notification_service(): void
    {
        Storage::fake('public');
        $masyarakat = $this->createUser('masyarakat');

        $this->mock(WebPushNotifier::class)
            ->shouldReceive('notifyAdminsOfNewReport')
            ->once()
            ->withArgs(fn (LaporanKeluhan $laporan): bool => $laporan->judul_laporan === 'Jalan berlubang');

        $this->actingAs($masyarakat, 'sanctum')
            ->post('/api/laporan', [
                'judul' => 'Jalan berlubang',
                'deskripsi' => 'Terdapat lubang besar di jalan utama.',
                'jenis_kerusakan' => 'jalan_berlubang',
                'foto' => [UploadedFile::fake()->image('laporan.jpg')],
            ])
            ->assertCreated()
            ->assertJsonPath('laporan.judul', 'Jalan berlubang');

        $this->assertDatabaseHas('laporan_keluhan', [
            'id_pelapor' => $masyarakat->id,
            'judul_laporan' => 'Jalan berlubang',
            'status' => 'menunggu',
        ]);

        $laporan = LaporanKeluhan::query()->latest('id')->firstOrFail();
        Storage::disk('public')->delete($laporan->foto_bukti_paths);
    }

    private function createUser(string $role): User
    {
        static $sequence = 0;
        $sequence++;

        return User::query()->create([
            'nama_lengkap' => 'Test Admin',
            'username' => 'admin-'.$sequence,
            'email' => 'admin-'.$sequence.'@example.test',
            'password' => Hash::make('password'),
            'peran' => $role,
            'status_akun' => 'aktif',
        ]);
    }
}
