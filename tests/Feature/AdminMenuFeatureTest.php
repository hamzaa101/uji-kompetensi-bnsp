<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\Category;
use App\Models\ErrorLog;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminMenuFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk();
    }

    public function test_admin_can_access_categories_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.categories.index'))->assertOk();
    }

    public function test_admin_can_access_suppliers_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.suppliers.index'))->assertOk();
    }

    public function test_admin_can_access_medicines_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.medicines.index'))->assertOk();
    }

    public function test_admin_can_access_medicine_batches_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.medicine-batches.index'))->assertOk();
    }

    public function test_admin_can_access_reports_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.reports.index'))->assertOk();
    }

    public function test_admin_can_access_imports_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.imports.index'))->assertOk();
    }

    public function test_admin_can_access_monitoring_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.monitoring.index'))->assertOk();
    }

    public function test_admin_can_access_error_logs_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.error-logs.index'))->assertOk();
    }

    public function test_admin_can_access_audit_logs_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.audit-logs.index'))->assertOk();
    }

    public function test_admin_can_access_simulations_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.simulations.index'))->assertOk();
    }

    public function test_pasien_cannot_access_admin_pages(): void
    {
        $patient = User::where('role', 'pasien')->firstOrFail();

        foreach ($this->adminPageRoutes() as $route) {
            $this->actingAs($patient)->get(route($route))->assertForbidden();
        }
    }

    public function test_admin_can_create_category(): void
    {
        $name = 'Kategori Feature '.Str::random(8);

        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'name' => $name,
                'description' => 'Kategori dari feature test.',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => $name]);
    }

    public function test_admin_can_create_supplier(): void
    {
        $name = 'Supplier Feature '.Str::random(8);

        $this->actingAs($this->admin())
            ->post(route('admin.suppliers.store'), [
                'name' => $name,
                'contact_person' => 'Admin Test',
                'phone' => '021-123456',
                'email' => 'supplier-feature@example.test',
                'address' => 'Jakarta',
            ])
            ->assertRedirect(route('admin.suppliers.index'));

        $this->assertDatabaseHas('suppliers', ['name' => $name]);
    }

    public function test_admin_can_create_medicine(): void
    {
        $name = 'Obat Feature '.Str::random(8);

        $this->actingAs($this->admin())
            ->post(route('admin.medicines.store'), [
                'category_id' => Category::firstOrFail()->id,
                'supplier_id' => Supplier::firstOrFail()->id,
                'name' => $name,
                'description' => 'Obat feature test.',
                'type' => 'obat_bebas',
                'price' => 12500,
                'min_stock' => 5,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.medicines.index'));

        $this->assertDatabaseHas('medicines', ['name' => $name]);
    }

    public function test_admin_can_create_medicine_batch(): void
    {
        $medicine = Medicine::firstOrFail();
        $batchNumber = 'FT-BATCH-'.Str::upper(Str::random(6));

        $this->actingAs($this->admin())
            ->post(route('admin.medicine-batches.store'), [
                'medicine_id' => $medicine->id,
                'batch_number' => $batchNumber,
                'quantity' => 12,
                'initial_quantity' => 12,
                'expiry_date' => now()->addMonths(8)->toDateString(),
                'purchase_price' => 7000,
                'received_at' => now()->toDateString(),
            ])
            ->assertRedirect(route('admin.medicine-batches.index'));

        $this->assertDatabaseHas('medicine_batches', [
            'medicine_id' => $medicine->id,
            'batch_number' => $batchNumber,
            'quantity' => 12,
        ]);
    }

    public function test_admin_can_filter_reports(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.reports.index', [
                'from' => now()->subDays(7)->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk();
    }

    public function test_admin_can_simulate_low_stock_alert(): void
    {
        $before = AppNotification::where('role_target', 'admin')
            ->where('title', 'Stok kritis')
            ->count();

        $this->actingAs($this->admin())
            ->post(route('admin.simulations.low-stock'))
            ->assertRedirect();

        $after = AppNotification::where('role_target', 'admin')
            ->where('title', 'Stok kritis')
            ->count();

        $this->assertGreaterThan($before, $after);
        $this->assertDatabaseHas('audit_logs', ['action' => 'simulate_low_stock_alert']);
    }

    public function test_admin_can_mark_error_log_as_resolved(): void
    {
        $log = ErrorLog::create([
            'severity' => 'warning',
            'message' => 'Feature test warning.',
            'is_resolved' => false,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.error-logs.resolve', $log))
            ->assertRedirect();

        $this->assertDatabaseHas('error_logs', [
            'id' => $log->id,
            'is_resolved' => true,
        ]);
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function adminPageRoutes(): array
    {
        return [
            'admin.dashboard',
            'admin.categories.index',
            'admin.suppliers.index',
            'admin.medicines.index',
            'admin.medicine-batches.index',
            'admin.reports.index',
            'admin.imports.index',
            'admin.monitoring.index',
            'admin.error-logs.index',
            'admin.audit-logs.index',
            'admin.simulations.index',
        ];
    }
}
