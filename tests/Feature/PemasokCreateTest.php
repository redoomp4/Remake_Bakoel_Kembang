<?php

namespace Tests\Feature;

use App\Models\Pemasok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemasokCreateTest extends TestCase
{
    use RefreshDatabase;

    private function penjual(): User
    {
        return User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-pemasok',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);
    }

    public function test_create_page_renders_simplified_form(): void
    {
        $response = $this->actingAs($this->penjual())->get(route('pemasok.create'));

        $response->assertOk();
        $response->assertSee('Tambah Pemasok Baru', false);
        $response->assertSee('Cukup isi', false);
        $response->assertSee('Informasi Tambahan', false);
        $response->assertSee('SIMPAN PEMASOK', false);
    }

    public function test_supplier_can_be_saved_with_only_name(): void
    {
        $user = $this->penjual();

        $response = $this->actingAs($user)->post(route('pemasok.store'), [
            'nama_pemasok' => 'Toko Bunga Melati',
        ]);

        $response->assertRedirect(route('pemasok.index'));
        $this->assertDatabaseHas('pemasoks', [
            'user_id' => $user->id,
            'nama_pemasok' => 'Toko Bunga Melati',
            'email' => null,
            'nama_pic' => null,
        ]);
    }

    public function test_supplier_name_over_database_limit_is_rejected_friendly(): void
    {
        $response = $this->actingAs($this->penjual())->post(route('pemasok.store'), [
            'nama_pemasok' => str_repeat('A', 151),
        ]);

        $response->assertSessionHasErrors('nama_pemasok');
        $this->assertSame(0, Pemasok::count());
    }
}
