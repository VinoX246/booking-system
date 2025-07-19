<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            
            // Include user relationship (conditionally)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ];
            }),
            
            // Additional computed attributes
            'duration' => $this->duration_in_minutes,
            'is_upcoming' => $this->start_time->isFuture(),
            
            // Links for HATEOAS
            'links' => [
                'self' => route('bookings.show', $this->id),
                'edit' => route('bookings.update', $this->id),
                'delete' => route('bookings.destroy', $this->id),
            ]
        ];
    }
    
    /**
     * Add additional meta data to the resource response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with(Request $request)
    {
        return [
            'meta' => [
                'version' => '1.0',
                'api_version' => config('app.version'),
                'copyright' => '© '.date('Y').' Your Company Name',
            ]
        ];
    }
}
