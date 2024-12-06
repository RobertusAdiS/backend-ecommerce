<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

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
        $categories = Category::when(Request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'.Request()->q.'%');
        })->latest()->paginate(5);

        //return api resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|unique:categories'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        //create category
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        if ($category) {
            //return success with api resource
            return new CategoryResource(true, 'Data Category Berhasil Disimpan', $category);
        }
        return new CategoryResource(false, 'Data Category Gagal Disimpan', null);
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $category = Category::whereId($id)->first();
        if ($category) {
            //return success with api resource
            return new CategoryResource(true, 'Detail Data Category', $category);
        }
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $category
     * @return void
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        //check image update
        if ($request->file('image')) {
            //remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            //update category with new image
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);
        }
        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            //return success with api resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate', $category);
        }
        //return failed with api resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate', null);
    }

    public function destroy(Category $category)
    {
        //remove image
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        if ($category->delete()) {
            //return success with api resource
            return new CategoryResource(true, 'Data Category Berhasil Dihapus', null);
        }

        //return failed with api resource
        return new CategoryResource(false, 'Data Category Gagal Dihapus', null);
    }
}
