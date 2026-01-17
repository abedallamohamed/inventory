<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(): AnonymousResourceCollection
    {
        $customers = Customer::with('orders')->get();
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request): CustomerResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $customer = Customer::create($validated);
        return new CustomerResource($customer);
    }

    /**
     * Display the specified customer.
     */
    public function show(string $id): CustomerResource
    {
        $customer = Customer::with('orders')->findOrFail($id);
        return new CustomerResource($customer);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, string $id): CustomerResource
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $customer->update($validated);
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified customer (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json(null, 204);
    }

    /**
     * Get all orders for the specified customer.
     */
    public function orders(string $id): AnonymousResourceCollection
    {
        $customer = Customer::findOrFail($id);
        $orders = $customer->orders()->get();
        return OrderResource::collection($orders);
    }
}
