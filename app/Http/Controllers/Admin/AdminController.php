<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentChat;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use App\Models\WarehouseItem;
use App\Models\Weighing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $stats = [
            'orders_today' => Order::whereDate('planned_date', $today)->count(),
            'orders_active' => Order::whereNotIn('status', ['closed'])->whereDate('planned_date', '>=', now()->subDays(3))->count(),
            'weighings_today' => Weighing::whereDate('weighed_at', $today)->count() + Order::whereDate('updated_at', $today)->whereNotNull('weight_netto')->count(),
            'warehouse_bales' => (int) WarehouseItem::sum('bales'),
            'users' => User::count(),
        ];

        $drivers = Driver::where('is_active', true)
            ->with(['todayOrders' => function ($q) use ($today) {
                $q->with(['client'])->whereDate('planned_date', $today)->orderBy('planned_time');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact('stats', 'drivers'));
    }

    public function driversIndex()
    {
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();
        $driver = $drivers->first();

        return redirect()->route('admin.drivers.show', $driver);
    }

    public function driversShow(Request $request, Driver $driver)
    {
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();

        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->subDays(7)->format('Y-m-d');
        $dateTo = $request->filled('date_to') ? $request->date_to : now()->format('Y-m-d');

        $query = Order::with(['client', 'tractor', 'trailer'])
            ->where('driver_id', $driver->id)
            ->whereDate('planned_date', '>=', $dateFrom)
            ->whereDate('planned_date', '<=', $dateTo);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('planned_date')->orderBy('planned_time')->get();

        return view('admin.drivers', compact('driver', 'drivers', 'orders'));
    }

    public function agentView()
    {
        $chats = AgentChat::orderByDesc('updated_at')->get();

        return view('admin.agent', compact('chats'));
    }

    public function agentChatSave(Request $request)
    {
        $chat = AgentChat::updateOrCreate(
            ['id' => $request->chat_id ?: null],
            [
                'title' => $request->title ?: 'Czat '.now()->format('d.m H:i'),
                'messages' => $request->messages,
                'user_id' => null,
            ]
        );

        return response()->json(['success' => true, 'chat_id' => $chat->id, 'title' => $chat->title]);
    }

    public function agentChatDelete(AgentChat $chat)
    {
        $chat->delete();

        return response()->json(['success' => true]);
    }

    public function agentChat(Request $request)
    {
        $apiKey = config('services.anthropic.key') ?? env('ANTHROPIC_API_KEY');
        $messages = $request->messages ?? [];
        $system = $request->system ?? '';

        if (! $apiKey) {
            return response()->json(['success' => false, 'error' => 'Brak klucza ANTHROPIC_API_KEY w pliku .env']);
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-opus-4-5',
            'max_tokens' => 2048,
            'system' => $system,
            'messages' => $messages,
        ]);

        if ($response->failed()) {
            return response()->json(['success' => false, 'error' => $response->json('error.message') ?? 'Błąd API']);
        }

        $content = $response->json('content.0.text') ?? '';

        return response()->json(['success' => true, 'content' => $content]);
    }
}
