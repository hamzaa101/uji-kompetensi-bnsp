<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\ErrorLog;
use App\Models\ImportJob;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\ResourceMetric;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = collect([
            ['name' => 'Admin Klinik', 'email' => 'admin@klinik.test', 'role' => 'admin'],
            ['name' => 'Apt. Sari Makmur', 'email' => 'apoteker@klinik.test', 'role' => 'apoteker'],
            ['name' => 'Kasir Budi', 'email' => 'kasir@klinik.test', 'role' => 'kasir'],
            ['name' => 'Pasien Demo', 'email' => 'pasien@klinik.test', 'role' => 'pasien', 'phone' => '081234567890', 'address' => 'Jl. Sehat No. 10'],
        ])->mapWithKeys(fn ($row) => [$row['role'] => User::create($row + ['password' => 'password123', 'is_active' => true])]);

        $categories = collect([
            'Obat Resep', 'Obat Bebas', 'Suplemen', 'Alat Kesehatan', 'Vitamin', 'Herbal',
        ])->mapWithKeys(fn ($name) => [$name => Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => "Kategori {$name} Klinik Makmur Jaya.",
        ])]);

        $suppliers = collect([
            ['name' => 'PT Sehat Sentosa', 'contact_person' => 'Rina', 'phone' => '021-111222', 'email' => 'sehat@example.test'],
            ['name' => 'CV Farma Jaya', 'contact_person' => 'Dedi', 'phone' => '021-333444', 'email' => 'farma@example.test'],
            ['name' => 'PT Medika Nusantara', 'contact_person' => 'Ayu', 'phone' => '021-555666', 'email' => 'medika@example.test'],
            ['name' => 'UD Herbal Prima', 'contact_person' => 'Wawan', 'phone' => '021-777888', 'email' => 'herbal@example.test'],
            ['name' => 'PT Alkes Mandiri', 'contact_person' => 'Tono', 'phone' => '021-999000', 'email' => 'alkes@example.test'],
        ])->map(fn ($row) => Supplier::create($row + ['address' => 'Jakarta']));

        $medicines = collect([
            ['Amoxicillin 500mg', 'Obat Resep', 'obat_resep', true, 18000],
            ['Cefixime 100mg', 'Obat Resep', 'obat_resep', true, 32000],
            ['Metformin 500mg', 'Obat Resep', 'obat_resep', true, 12000],
            ['Amlodipine 5mg', 'Obat Resep', 'obat_resep', true, 15000],
            ['Simvastatin 20mg', 'Obat Resep', 'obat_resep', true, 17000],
            ['Paracetamol 500mg', 'Obat Bebas', 'obat_bebas', false, 6000],
            ['Ibuprofen 200mg', 'Obat Bebas', 'obat_bebas', false, 9000],
            ['Cetirizine 10mg', 'Obat Bebas', 'obat_bebas', false, 8500],
            ['Antasida Doen', 'Obat Bebas', 'obat_bebas', false, 7000],
            ['Oralit Sachet', 'Obat Bebas', 'obat_bebas', false, 3500],
            ['Vitamin C 500mg', 'Vitamin', 'suplemen', false, 11000],
            ['Vitamin D3 1000 IU', 'Vitamin', 'suplemen', false, 28000],
            ['Multivitamin Anak', 'Vitamin', 'suplemen', false, 24000],
            ['Zinc Tablet', 'Suplemen', 'suplemen', false, 10000],
            ['Fish Oil Softgel', 'Suplemen', 'suplemen', false, 45000],
            ['Masker Medis 50pcs', 'Alat Kesehatan', 'alat_kesehatan', false, 35000],
            ['Termometer Digital', 'Alat Kesehatan', 'alat_kesehatan', false, 55000],
            ['Plester Luka', 'Alat Kesehatan', 'alat_kesehatan', false, 8000],
            ['Hand Sanitizer 100ml', 'Alat Kesehatan', 'alat_kesehatan', false, 15000],
            ['Tensimeter Digital', 'Alat Kesehatan', 'alat_kesehatan', false, 260000],
            ['Minyak Kayu Putih', 'Herbal', 'obat_bebas', false, 18000],
            ['Madu Herbal', 'Herbal', 'suplemen', false, 38000],
            ['Tolak Angin Cair', 'Herbal', 'obat_bebas', false, 5000],
            ['Jahe Merah Sachet', 'Herbal', 'suplemen', false, 12000],
            ['Kunyit Asam Kapsul', 'Herbal', 'suplemen', false, 26000],
            ['Salbutamol 2mg', 'Obat Resep', 'obat_resep', true, 13000],
            ['Omeprazole 20mg', 'Obat Resep', 'obat_resep', true, 14000],
            ['Loperamide 2mg', 'Obat Bebas', 'obat_bebas', false, 7500],
            ['Povidone Iodine', 'Obat Bebas', 'obat_bebas', false, 16000],
            ['Gauze Sterile', 'Alat Kesehatan', 'alat_kesehatan', false, 9000],
        ]);

        $createdMedicines = $medicines->map(function ($row, $index) use ($categories, $suppliers) {
            [$name, $category, $type, $requiresPrescription, $price] = $row;
            $medicine = Medicine::create([
                'category_id' => $categories[$category]->id,
                'supplier_id' => $suppliers[$index % $suppliers->count()]->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Informasi lengkap {$name} untuk demonstrasi katalog Klinik Makmur Jaya.",
                'composition' => "Komposisi utama {$name}.",
                'dosage' => 'Gunakan sesuai aturan pakai atau arahan tenaga kesehatan.',
                'side_effects' => 'Hentikan penggunaan jika muncul reaksi alergi dan konsultasikan ke klinik.',
                'type' => $type,
                'requires_prescription' => $requiresPrescription,
                'price' => $price,
                'min_stock' => in_array($index, [1, 8, 17, 25], true) ? 15 : 10,
                'is_active' => true,
            ]);

            $baseQty = in_array($index, [1, 8, 17, 25], true) ? 4 : 30 + ($index % 5) * 8;
            foreach ([0, 1] as $batchIndex) {
                $quantity = $batchIndex === 0 ? max(1, (int) floor($baseQty / 2)) : $baseQty;
                $expiry = match (true) {
                    $index % 9 === 0 => now()->addDays(25 + $batchIndex * 20),
                    $index % 7 === 0 => now()->addDays(55 + $batchIndex * 25),
                    $index % 5 === 0 => now()->addDays(85 + $batchIndex * 30),
                    default => now()->addDays(160 + $index + $batchIndex * 60),
                };

                $medicine->batches()->create([
                    'batch_number' => 'B'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT).'-'.($batchIndex + 1),
                    'quantity' => $quantity,
                    'initial_quantity' => $quantity + 5,
                    'expiry_date' => $expiry->toDateString(),
                    'purchase_price' => max(1000, $price * 0.65),
                    'received_at' => now()->subDays(15 + $index)->toDateString(),
                ]);
            }

            return $medicine;
        });

        $this->seedOrders($users, $createdMedicines);
        $this->seedLogs($users);
        $this->seedSampleCsv();
    }

    private function seedOrders($users, $medicines): void
    {
        $paracetamol = $medicines->firstWhere('name', 'Paracetamol 500mg');
        $vitaminC = $medicines->firstWhere('name', 'Vitamin C 500mg');
        $amoxicillin = $medicines->firstWhere('name', 'Amoxicillin 500mg');
        $masker = $medicines->firstWhere('name', 'Masker Medis 50pcs');
        $termometer = $medicines->firstWhere('name', 'Termometer Digital');
        $antasida = $medicines->firstWhere('name', 'Antasida Doen');

        $this->createCompletedOrder($users['pasien'], null, 'ONL-DEMO-001', 'online', [[$paracetamol, 2], [$vitaminC, 1]]);
        $this->createCompletedOrder($users['pasien'], null, 'ONL-DEMO-002', 'online', [[$antasida, 2]]);
        $this->createCompletedOrder(null, $users['kasir'], 'KSR-DEMO-001', 'offline', [[$masker, 1], [$termometer, 1]]);
        $this->createCompletedOrder(null, $users['kasir'], 'KSR-DEMO-002', 'offline', [[$paracetamol, 1], [$antasida, 1]]);

        Storage::disk('public')->put('prescriptions/demo-prescription.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='
        ));

        $order = Order::create([
            'user_id' => $users['pasien']->id,
            'order_number' => 'ONL-DEMO-003',
            'channel' => 'online',
            'status' => 'waiting_prescription',
            'payment_method' => 'transfer',
            'payment_status' => 'waiting_confirmation',
            'total_amount' => $amoxicillin->price,
            'notes' => 'Order demo resep pending.',
        ]);
        $order->items()->create([
            'medicine_id' => $amoxicillin->id,
            'quantity' => 1,
            'price' => $amoxicillin->price,
            'subtotal' => $amoxicillin->price,
        ]);
        $order->prescription()->create([
            'user_id' => $users['pasien']->id,
            'image_path' => 'prescriptions/demo-prescription.png',
            'status' => 'pending',
        ]);
    }

    private function createCompletedOrder(?User $user, ?User $cashier, string $number, string $channel, array $items): void
    {
        $total = collect($items)->sum(fn ($row) => (float) $row[0]->price * $row[1]);
        $order = Order::create([
            'user_id' => $user?->id,
            'cashier_id' => $cashier?->id,
            'order_number' => $number,
            'channel' => $channel,
            'status' => 'completed',
            'payment_method' => $channel === 'offline' ? 'cash' : 'transfer',
            'payment_status' => 'paid',
            'total_amount' => $total,
            'created_at' => now()->subDays(random_int(0, 12)),
            'updated_at' => now()->subDays(random_int(0, 12)),
        ]);

        foreach ($items as [$medicine, $quantity]) {
            $order->items()->create([
                'medicine_id' => $medicine->id,
                'quantity' => $quantity,
                'price' => $medicine->price,
                'subtotal' => (float) $medicine->price * $quantity,
            ]);

            $batch = $medicine->batches()->where('quantity', '>=', $quantity)->orderBy('expiry_date')->first();
            if ($batch) {
                $batch->decrement('quantity', $quantity);
                StockMovement::create([
                    'medicine_id' => $medicine->id,
                    'medicine_batch_id' => $batch->id,
                    'order_id' => $order->id,
                    'type' => 'out',
                    'quantity' => $quantity,
                    'description' => 'Seed demo completed order',
                    'created_by' => $cashier?->id ?? $user?->id,
                ]);
            }
        }
    }

    private function seedLogs($users): void
    {
        foreach ([
            ['admin', 'login', 'Admin login demo'],
            ['admin', 'create_medicine', 'Admin membuat data obat'],
            ['admin', 'export_report_pdf', 'Admin export laporan PDF'],
            ['admin', 'queue_import_csv', 'Admin upload CSV obat'],
            ['apoteker', 'login', 'Apoteker login demo'],
            ['apoteker', 'approve_prescription', 'Apoteker approve resep'],
            ['kasir', 'login', 'Kasir login demo'],
            ['kasir', 'cashier_checkout', 'Kasir transaksi offline'],
            ['pasien', 'login', 'Pasien login demo'],
            ['pasien', 'checkout', 'Pasien checkout online'],
        ] as $row) {
            AuditLog::create([
                'user_id' => $users[$row[0]]->id,
                'action' => $row[1],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder Demo',
                'description' => $row[2],
                'created_at' => now()->subHours(random_int(1, 72)),
                'updated_at' => now()->subHours(random_int(1, 72)),
            ]);
        }

        foreach ([
            [null, 'admin', 'Stok kritis', 'Cefixime 100mg tersisa sedikit.', 'warning'],
            [null, 'apoteker', 'Obat hampir kedaluwarsa', 'Batch B001-1 perlu dipantau.', 'warning'],
            [$users['pasien']->id, null, 'Order diproses', 'Order ONL-DEMO-001 sudah selesai.', 'success'],
            [null, 'admin', 'Pesanan baru', 'Order ONL-DEMO-003 perlu verifikasi resep.', 'info'],
            [null, 'admin', 'Error aplikasi', 'Simulasi error critical belum resolved.', 'critical'],
        ] as $row) {
            AppNotification::create([
                'user_id' => $row[0],
                'role_target' => $row[1],
                'title' => $row[2],
                'message' => $row[3],
                'type' => $row[4],
            ]);
        }

        foreach ([
            ['info', 'Import CSV demo selesai.'],
            ['warning', 'Upload resep ditolak karena format tidak valid.'],
            ['critical', 'Simulasi error critical pada checkout.'],
        ] as $row) {
            ErrorLog::create(['severity' => $row[0], 'message' => $row[1]]);
        }

        ResourceMetric::create([
            'memory_usage' => 28 * 1024 * 1024,
            'disk_usage' => 1024 * 1024 * 120,
            'queue_pending' => 0,
            'request_count' => 4,
            'error_count' => 3,
            'avg_response_time' => 35,
        ]);

        ImportJob::create([
            'filename' => 'examples/sample_medicines.csv',
            'original_name' => 'sample_medicines.csv',
            'status' => 'completed',
            'total_rows' => 3,
            'processed_rows' => 3,
            'created_by' => $users['admin']->id,
        ]);
    }

    private function seedSampleCsv(): void
    {
        Storage::disk('local')->put('examples/sample_medicines.csv', implode("\n", [
            'name,category,supplier,type,requires_prescription,price,min_stock,quantity,initial_quantity,batch_number,expiry_date,purchase_price,received_at,description,composition,dosage,side_effects',
            'Demo Flu Tablet,Obat Bebas,PT Sehat Sentosa,obat_bebas,false,9000,10,50,50,IMP-FLU-001,'.now()->addMonths(8)->toDateString().',5000,'.now()->toDateString().',Obat flu demo,Paracetamol campuran,Sesuai aturan pakai,Kantuk ringan',
            'Demo Antibiotik,Obat Resep,CV Farma Jaya,obat_resep,true,22000,10,25,25,IMP-AB-001,'.now()->addMonths(10)->toDateString().',15000,'.now()->toDateString().',Antibiotik demo,Amoxicillin,Dengan resep dokter,Alergi',
            'Demo Vitamin,Vitamin,PT Medika Nusantara,suplemen,false,18000,10,80,80,IMP-VIT-001,'.now()->addYear()->toDateString().',10000,'.now()->toDateString().',Vitamin demo,Vitamin C,1x sehari,Mual ringan',
        ]));
    }
}
