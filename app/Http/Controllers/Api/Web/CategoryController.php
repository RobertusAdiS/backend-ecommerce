<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get categories
        $categories = Category::latest()->get();
        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories',$categories);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($slug)
    {
        $category = Category::with('products.category')
        //get count rreview and average review
        ->with('products', function($query){
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');
        }) ->where('slug', $slug)->first();
        if($category){
            //return success with Api Resource
            return new CategoryResource(true, 'Data Product By Category : '.$category->name.'', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Product By Category Not Found', null);
    }

}
