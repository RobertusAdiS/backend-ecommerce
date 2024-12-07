<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get products
        $products = Product::with('category')
        //count and avarage
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        //search
        ->when(request()->q, function($products) {
            $products = $products->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(8);
        //return with Api Resource
        return new ProductResource(true, 'List Data Products',$products);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $product = Product::with('category', 'reviews.customer')
        //get count rreview and average review
        ->withAvg('reviews', 'rating') 
        ->withCount('reviews')
        ->where('slug', $slug)->first();
        if($product){
            //return success with Api Resource
            return new ProductResource(true, 'Detail Data Product', $product);
        }

        //return failed with Api Resource
        return new ProductResource(false, 'Data Product By Slug Not Found', null);
    }
}
