<?php

// app/Http/Requests/UpdateBookingRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->booking);
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
        ];
    }
}
