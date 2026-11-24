<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Debtor;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $from  = ($request->date('from') ?? $today->copy()->startOfMonth())->copy()->startOfDay();
        $to    = ($request->date('to') ?? $today->copy())->copy()->startOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $cur = $this->stats($from, $to);

        // Periode sebelumnya dengan panjang hari yang sama (untuk perbandingan)
        $days     = (int) $from->diffInDays($to) + 1;
        $prevTo   = $from->copy()->subDay();
        $prevFrom = $prevTo->copy()->subDays($days - 1);
        $prev     = $this->stats($prevFrom, $prevTo);

        $pct = fn ($c, $p) => $p == 0 ? null : round(($c - $p) / abs($p) * 100, 1);
        $trends = [
            'net'     => $pct($cur['netProfit'], $prev['netProfit']),
            'income'  => $pct($cur['income'], $prev['income']),
            'expense' => $pct($cur['expense'], $prev['expense']),
        ];

        $margin = $cur['revenue'] > 0 ? round($cur['salesProfit'] / $cur['revenue'] * 100, 1) : null;

        $preset = 'custom';
        if ($from->isSameDay($to) && $to->isSameDay($today)) {
            $preset = 'today';
        } elseif ($to->isSameDay($today) && $from->isSameDay($today->copy()->subDays(6))) {
            $preset = '7d';
        } elseif ($to->isSameDay($today) && $from->isSameDay($today->copy()->startOfMonth())) {
            $preset = 'month';
        }

        $pendingOrders = Order::with('items')->where('status', 'pending')->latest()->limit(5)->get();
        $pendingCount  = Order::where('status', 'pending')->count();
        $lowStock      = Product::where('stock', '<=', 5)->orderBy('stock')->limit(6)->get();
        $lowStockCount = Product::where('stock', '<=', 5)->count();

        $openDebts    = Debtor::outstanding();
        $kasbonTotal  = $openDebts->sum('balance');
        $kasbonCount  = $openDebts->count();

        return view('seller.dashboard', compact(
            'from', 'to', 'preset', 'cur', 'trends', 'margin',
            'pendingOrders', 'pendingCount', 'lowStock', 'lowStockCount',
            'kasbonTotal', 'kasbonCount'
        ));
    }

    private function stats(CarbonInterface $from, CarbonInterface $to): array
    {
        $range = Transaction::whereBetween('date', [$from->toDateString(), $to->toDateString()]);

        $income    = (int) (clone $range)->where('type', 'in')->sum('amount');
        $expense   = (int) (clone $range)->where('type', 'out')->sum('amount');
        $otherCost = (int) (clone $range)->where('type', 'out')->whereNull('product_id')->sum('amount');

        $sales = OrderItem::profitBetween($from, $to);

        return [
            'income'      => $income,
            'expense'     => $expense,
            'otherCost'   => $otherCost,
            'stockCost'   => $expense - $otherCost,
            'salesProfit' => $sales['profit'],
            'revenue'     => $sales['revenue'],
            'orders'      => $sales['orders'],
            'noCost'      => $sales['nocost'],
            'netProfit'   => $sales['profit'] - $otherCost,
        ];
    }
}
