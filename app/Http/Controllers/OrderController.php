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
    public function store(Request $request): OrderResource|JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'order_number' => 'required|string|unique:orders,order_number',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::create($validator->validated());
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
    public function update(Request $request, string $id): OrderResource|JsonResponse
    {
        $order = Order::findOrFail($id);

        // Check if order can be modified
        if (!$order->canBeModified()) {
            return response()->json([
                'message' => 'Cannot modify order',
                'errors' => [
                    'order' => ['Orders with status "' . $order->status . '" cannot be modified.']
                ]
            ], 422);
        }

        $validated = $request->validate([
            'order_number' => 'sometimes|required|string|unique:orders,order_number,' . $id,
            'customer_id' => 'sometimes|required|exists:customers,id',
            'order_date' => 'sometimes|required|date',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
        ]);

        // Check status transition if status is being updated
        if (isset($validated['status']) && $validated['status'] !== $order->status) {
            if (!$order->canTransitionToStatus($validated['status'])) {
                return response()->json([
                    'message' => 'Invalid status transition',
                    'errors' => [
                        'status' => ['Cannot change status from "' . $order->status . '" to "' . $validated['status'] . '".']
                    ]
                ], 422);
            }
        }

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
        
        // Check if order can be modified (deleted)
        if (!$order->canBeModified()) {
            return response()->json([
                'message' => 'Cannot delete order',
                'errors' => [
                    'order' => ['Orders with status "' . $order->status . '" cannot be deleted.']
                ]
            ], 422);
        }
        
        $order->delete();
        return response()->json(null, 204);
    }
}
