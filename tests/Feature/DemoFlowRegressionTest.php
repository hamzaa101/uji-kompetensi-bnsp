<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoFlowRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_patient_can_view_catalog_and_use_search_autocomplete_endpoint(): void
    {
        $patient = $this->user('pasien');

        $this->actingAs($patient)
            ->get(route('catalog.index', ['search' => 'Paracetamol', 'type' => 'obat_bebas']))
            ->assertOk()
            ->assertSee('Katalog Obat')
            ->assertSee('Paracetamol 500mg');

        $this->actingAs($patient)
            ->getJson(route('catalog.autocomplete', ['q' => 'para']))
            ->assertOk()
            ->assertJsonFragment(['label' => 'Paracetamol 500mg']);
    }

    public function test_admin_demo_pages_pdf_and_import_csv_are_accessible(): void
    {
        Storage::fake('local');

        $admin = $this->user('admin');

        foreach ([
            route('admin.reports.index'),
            route('admin.imports.index'),
            route('admin.monitoring.index'),
            route('admin.audit-logs.index'),
            route('admin.error-logs.index'),
            route('admin.simulations.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)
            ->get(route('admin.reports.pdf'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $csv = UploadedFile::fake()->createWithContent('regression_medicines.csv', implode("\n", [
            'name,category,supplier,type,requires_prescription,price,min_stock,quantity,initial_quantity,batch_number,expiry_date,purchase_price,received_at,description,composition,dosage,side_effects',
            'Regression Import Obat,Obat Bebas,PT Regression,obat_bebas,false,12000,5,9,9,REG-IMP-001,'.now()->addYear()->toDateString().',7000,'.now()->toDateString().',Demo import,Komposisi demo,1x sehari,-',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.imports.store'), ['csv' => $csv])
            ->assertRedirect();

        $this->assertDatabaseHas('import_jobs', [
            'original_name' => 'regression_medicines.csv',
            'status' => 'completed',
            'processed_rows' => 1,
        ]);
        $this->assertDatabaseHas('medicines', ['name' => 'Regression Import Obat']);
    }

    public function test_checkout_free_medicine_completes_and_deducts_fifo_batch_first(): void
    {
        $patient = $this->user('pasien');
        [$medicine, $earlyBatch, $lateBatch] = $this->medicineWithBatches(false, 5, 5);

        $cart = Cart::firstOrCreate(['user_id' => $patient->id]);
        $cart->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => 7,
            'price_snapshot' => $medicine->price,
        ]);

        $this->actingAs($patient)
            ->post(route('checkout.store'), ['payment_method' => 'transfer'])
            ->assertRedirect();

        $order = Order::where('user_id', $patient->id)->latest('id')->firstOrFail();

        $this->assertSame('completed', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(0, $earlyBatch->refresh()->quantity);
        $this->assertSame(3, $lateBatch->refresh()->quantity);
        $this->assertDatabaseHas('stock_movements', [
            'medicine_batch_id' => $earlyBatch->id,
            'order_id' => $order->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'medicine_batch_id' => $lateBatch->id,
            'order_id' => $order->id,
            'quantity' => 2,
        ]);
    }

    public function test_checkout_prescription_medicine_requires_prescription_upload(): void
    {
        $patient = $this->user('pasien');
        [$medicine] = $this->medicineWithBatches(true, 4, 0);
        $ordersBefore = Order::count();

        $cart = Cart::firstOrCreate(['user_id' => $patient->id]);
        $cart->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => 1,
            'price_snapshot' => $medicine->price,
        ]);

        $this->actingAs($patient)
            ->from(route('checkout.create'))
            ->post(route('checkout.store'), ['payment_method' => 'transfer'])
            ->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors('prescription');

        $this->assertSame($ordersBefore, Order::count());
    }

    public function test_prescription_checkout_upload_then_apoteker_approval_deducts_stock_fifo(): void
    {
        Storage::fake('public');

        $patient = $this->user('pasien');
        $apoteker = $this->user('apoteker');
        [$medicine, $earlyBatch, $lateBatch] = $this->medicineWithBatches(true, 3, 4);

        $cart = Cart::firstOrCreate(['user_id' => $patient->id]);
        $cart->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => 5,
            'price_snapshot' => $medicine->price,
        ]);

        $this->actingAs($patient)
            ->post(route('checkout.store'), [
                'payment_method' => 'transfer',
                'prescription' => UploadedFile::fake()->image('resep.png'),
            ])
            ->assertRedirect();

        $order = Order::with('prescription')->where('user_id', $patient->id)->latest('id')->firstOrFail();

        $this->assertSame('waiting_prescription', $order->status);
        $this->assertSame('pending', $order->prescription->status);
        $this->assertSame(3, $earlyBatch->refresh()->quantity);
        $this->assertSame(4, $lateBatch->refresh()->quantity);

        $this->actingAs($apoteker)
            ->post(route('apoteker.prescriptions.approve', $order), ['notes' => 'Resep valid'])
            ->assertRedirect(route('apoteker.prescriptions'));

        $order->refresh()->load('prescription');

        $this->assertSame('processing', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('approved', $order->prescription->status);
        $this->assertSame(0, $earlyBatch->refresh()->quantity);
        $this->assertSame(2, $lateBatch->refresh()->quantity);
    }

    public function test_kasir_can_create_offline_sale_with_blank_default_form_rows(): void
    {
        $kasir = $this->user('kasir');
        [$medicine, $earlyBatch] = $this->medicineWithBatches(false, 6, 0);

        $this->actingAs($kasir)
            ->post(route('kasir.sales.store'), [
                'payment_method' => 'cash',
                'items' => [
                    ['medicine_id' => $medicine->id, 'quantity' => 2],
                    ['medicine_id' => '', 'quantity' => ''],
                    ['medicine_id' => '', 'quantity' => ''],
                ],
            ])
            ->assertOk()
            ->assertSee('Struk');

        $order = Order::where('channel', 'offline')->latest('id')->firstOrFail();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(4, $earlyBatch->refresh()->quantity);
    }

    private function user(string $role): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    /**
     * @return array{0: Medicine, 1: MedicineBatch, 2: MedicineBatch}
     */
    private function medicineWithBatches(bool $requiresPrescription, int $earlyQty, int $lateQty): array
    {
        $name = 'Regression '.Str::uuid()->toString();

        $medicine = Medicine::create([
            'category_id' => Category::firstOrFail()->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => $requiresPrescription ? 'obat_resep' : 'obat_bebas',
            'requires_prescription' => $requiresPrescription,
            'price' => 15000,
            'min_stock' => 1,
            'is_active' => true,
        ]);

        $earlyBatch = $medicine->batches()->create([
            'batch_number' => 'REG-EARLY-'.Str::random(6),
            'quantity' => $earlyQty,
            'initial_quantity' => $earlyQty,
            'expiry_date' => now()->addDays(15)->toDateString(),
        ]);

        $lateBatch = $medicine->batches()->create([
            'batch_number' => 'REG-LATE-'.Str::random(6),
            'quantity' => $lateQty,
            'initial_quantity' => $lateQty,
            'expiry_date' => now()->addDays(60)->toDateString(),
        ]);

        return [$medicine, $earlyBatch, $lateBatch];
    }
}
