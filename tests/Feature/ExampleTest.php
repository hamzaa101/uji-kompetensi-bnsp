<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed();
    }

    public function test_user_can_login(): void
    {
        $user = User::where('email', 'admin@klinik.test')->firstOrFail();

        $this->post('/login', [
            'email' => 'admin@klinik.test',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $patient = User::where('role', 'pasien')->firstOrFail();

        $this->actingAs($patient)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()->assertSee('Dashboard Admin');
    }

    public function test_admin_can_create_medicine(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $category = Category::firstOrFail();

        $this->actingAs($admin)->post('/admin/medicines', [
            'category_id' => $category->id,
            'name' => 'Obat Test Feature',
            'type' => 'obat_bebas',
            'price' => 10000,
            'min_stock' => 5,
            'is_active' => 1,
            'description' => 'Feature test',
        ])->assertRedirect('/admin/medicines');

        $this->assertDatabaseHas('medicines', ['name' => 'Obat Test Feature']);
    }

    public function test_patient_can_add_medicine_to_cart(): void
    {
        $patient = User::where('role', 'pasien')->firstOrFail();
        $medicine = Medicine::where('requires_prescription', false)->firstOrFail();

        $this->actingAs($patient)->post(route('cart.add', $medicine), [
            'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['medicine_id' => $medicine->id, 'quantity' => 1]);
    }

    public function test_checkout_fails_if_stock_is_not_enough(): void
    {
        $patient = User::where('role', 'pasien')->firstOrFail();
        $category = Category::firstOrFail();
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Stok Tipis Test',
            'slug' => 'stok-tipis-test',
            'type' => 'obat_bebas',
            'requires_prescription' => false,
            'price' => 1000,
            'min_stock' => 1,
        ]);
        $medicine->batches()->create([
            'batch_number' => 'LOW-001',
            'quantity' => 1,
            'initial_quantity' => 1,
            'expiry_date' => now()->addMonth(),
        ]);
        $cart = Cart::firstOrCreate(['user_id' => $patient->id]);
        $cart->items()->create(['medicine_id' => $medicine->id, 'quantity' => 2, 'price_snapshot' => 1000]);

        $this->actingAs($patient)
            ->from(route('checkout.create'))
            ->post(route('checkout.store'), ['payment_method' => 'transfer'])
            ->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors('stock');
    }

    public function test_apoteker_can_approve_prescription(): void
    {
        $apoteker = User::where('role', 'apoteker')->firstOrFail();
        $order = Order::where('order_number', 'ONL-DEMO-003')->firstOrFail();

        $this->actingAs($apoteker)
            ->post(route('apoteker.prescriptions.approve', $order), ['notes' => 'Resep valid'])
            ->assertRedirect(route('apoteker.prescriptions'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
        $this->assertDatabaseHas('prescriptions', ['order_id' => $order->id, 'status' => 'approved']);
    }

    public function test_kasir_can_create_offline_sale(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $medicine = Medicine::where('requires_prescription', false)->firstOrFail();

        $this->actingAs($kasir)->post(route('kasir.sales.store'), [
            'payment_method' => 'cash',
            'items' => [
                ['medicine_id' => $medicine->id, 'quantity' => 1],
            ],
        ])->assertOk()->assertSee('Struk');

        $this->assertDatabaseHas('orders', ['channel' => 'offline', 'payment_status' => 'paid']);
    }

    public function test_admin_can_access_report_page(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->get('/admin/reports')->assertOk()->assertSee('Laporan Penjualan');
    }
}
