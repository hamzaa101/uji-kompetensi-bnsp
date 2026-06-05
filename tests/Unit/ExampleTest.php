<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Medicine;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifo_stock_deduction_uses_nearest_expiry_first(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'FIFO Test Medicine',
            'slug' => 'fifo-test-medicine',
            'type' => 'obat_bebas',
            'requires_prescription' => false,
            'price' => 1000,
            'min_stock' => 1,
        ]);

        $first = $medicine->batches()->create([
            'batch_number' => 'FIFO-OLD',
            'quantity' => 5,
            'initial_quantity' => 5,
            'expiry_date' => now()->addDays(10),
        ]);
        $second = $medicine->batches()->create([
            'batch_number' => 'FIFO-NEW',
            'quantity' => 5,
            'initial_quantity' => 5,
            'expiry_date' => now()->addDays(60),
        ]);

        app(StockService::class)->deductFifo($medicine, 7);

        $this->assertSame(0, $first->fresh()->quantity);
        $this->assertSame(3, $second->fresh()->quantity);
        $this->assertDatabaseHas('stock_movements', ['medicine_batch_id' => $first->id, 'quantity' => 5]);
        $this->assertDatabaseHas('stock_movements', ['medicine_batch_id' => $second->id, 'quantity' => 2]);
    }
}
