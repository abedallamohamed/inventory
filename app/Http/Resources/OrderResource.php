<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_date' => $this->order_date->format('d/m/Y'),
            'total_amount' => number_format($this->total_amount, 2),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'customer' => $this->whenLoaded('customer', new CustomerResource($this->customer)),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Get human readable status label.
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'In Attesa',
            'processing' => 'In Lavorazione', 
            'completed' => 'Completato',
            'cancelled' => 'Annullato',
            default => ucfirst($this->status),
        };
    }
}
