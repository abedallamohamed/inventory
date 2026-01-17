<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with('customer')->get();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): OrderResource
    {
        $validated = $request->validate([
            'order_number' => 'required|string|unique:orders,order_number',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::create($validated);
        $order->load('customer');
        return new OrderResource($order);
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): OrderResource
    {
        $order = Order::with('customer')->findOrFail($id);
        return new OrderResource($order);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, string $id): OrderResource
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'order_number' => 'sometimes|required|string|unique:orders,order_number,' . $id,
            'customer_id' => 'sometimes|required|exists:customers,id',
            'order_date' => 'sometimes|required|date',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);
        $order->load('customer');
        return new OrderResource($order);
    }

    /**
     * Remove the specified order (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
