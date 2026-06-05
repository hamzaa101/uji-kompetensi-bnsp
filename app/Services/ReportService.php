<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dashboardStats(): array
    {
        $stock = app(StockService::class);

        return [
            'today_sales' => (float) DB::table('orders')->whereDate('created_at', now()->toDateString())->where('payment_status', 'paid')->sum('total_amount'),
            'order_count' => DB::table('orders')->count(),
            'active_medicines' => DB::table('medicines')->where('is_active', true)->count(),
            'critical_stock' => $stock->criticalMedicines()->count(),
            'expiring' => $stock->expiringBatches(90)->count(),
            'month_revenue' => (float) DB::table('orders')->where('payment_status', 'paid')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'),
        ];
    }

    public function salesPerDay(string $from, string $to): array
    {
        // Raw SQL aman dengan parameter binding untuk memenuhi unit kompetensi "Menggunakan SQL".
        $sql = "select {$this->dateExpr('created_at')} as period, count(*) as transactions, coalesce(sum(total_amount), 0) as revenue
                from orders
                where created_at between ? and ? and payment_status = ?
                group by {$this->dateExpr('created_at')}
                order by period";

        return DB::select($sql, [$from.' 00:00:00', $to.' 23:59:59', 'paid']);
    }

    public function salesPerMonth(string $from, string $to): array
    {
        $sql = "select {$this->monthExpr('created_at')} as period, count(*) as transactions, coalesce(sum(total_amount), 0) as revenue
                from orders
                where created_at between ? and ? and payment_status = ?
                group by {$this->monthExpr('created_at')}
                order by period";

        return DB::select($sql, [$from.' 00:00:00', $to.' 23:59:59', 'paid']);
    }

    public function topMedicines(string $from, string $to): array
    {
        $sql = 'select m.name, sum(oi.quantity) as sold_qty, sum(oi.subtotal) as revenue
                from order_items oi
                join orders o on o.id = oi.order_id
                join medicines m on m.id = oi.medicine_id
                where o.created_at between ? and ? and o.payment_status = ?
                group by m.id, m.name
                order by sold_qty desc
                limit 10';

        return DB::select($sql, [$from.' 00:00:00', $to.' 23:59:59', 'paid']);
    }

    public function expiringMedicines(int $days = 90): array
    {
        $sql = 'select m.name, mb.batch_number, mb.quantity, mb.expiry_date
                from medicine_batches mb
                join medicines m on m.id = mb.medicine_id
                where mb.quantity > 0 and mb.expiry_date between ? and ?
                order by mb.expiry_date asc';

        return DB::select($sql, [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function orderStatusRecap(string $from, string $to): array
    {
        $sql = 'select status, count(*) as total, coalesce(sum(total_amount), 0) as revenue
                from orders
                where created_at between ? and ?
                group by status
                order by total desc';

        return DB::select($sql, [$from.' 00:00:00', $to.' 23:59:59']);
    }

    public function criticalStock(): array
    {
        $sql = 'select m.id, m.name, m.min_stock, coalesce(sum(mb.quantity), 0) as stock
                from medicines m
                left join medicine_batches mb on mb.medicine_id = m.id
                where m.is_active = 1
                group by m.id, m.name, m.min_stock
                having coalesce(sum(mb.quantity), 0) <= m.min_stock
                order by stock asc, m.name asc';

        return DB::select($sql);
    }

    private function dateExpr(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite' ? "date({$column})" : "DATE({$column})";
    }

    private function monthExpr(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite' ? "strftime('%Y-%m', {$column})" : "DATE_FORMAT({$column}, '%Y-%m')";
    }
}
