<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Review;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $check_review = Review::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();
        if($check_review) {
            return response()->json($check_review, 409);
        }

        $review = Review::create([
            'customer_id' => auth()->guard('api_customer')->user()->id,
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        if($review) {
            return new ReviewResource(true, 'Review Berhasil Disimpan', $review);
        }

        return new ReviewResource(false, 'Review Gagal Dibuat', null);
    }
}
