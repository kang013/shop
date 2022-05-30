<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SendReviewRequest extends Request
{
    public function rules()
    {
        return [
            'id'     => [
                'required',
                Rule::exists('order_items', 'id')->where('order_id', $this->route('order')->id)
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'rating' => '评分',
            'review' => '评价',
        ];
    }
}
