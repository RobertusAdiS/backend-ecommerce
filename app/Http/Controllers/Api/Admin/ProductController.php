<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get data product
        $products = Product::with('category')->when(request()->q, function($products) {
            $products->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        //return with Api Resource
        return new ProductResource(true, 'List Data Products',$products);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|unique:products',
            'category_id' => 'required',
            'description' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'discount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api_admin')->user()->id,
            'description' => $request->description,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount
        ]);

        //return success with Api Resource
        if ($product) {
            return new ProductResource(true, 'Data Product Berhasil Disimpan', $product);
        }

        //return failed with Api Resource
        return new ProductResource(false, 'Data Product Gagal Disimpan', null);
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $product = Product::whereId($id)->first();
        if($product) {
            //return success with Api Resource
            return new ProductResource(true, 'Detail Data Product!',$product);
        }
        //return failed with Api Resource
        return new ProductResource(false, 'Detail Data Product Tidak Ditemukan!', null);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $product
     * @return void
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title'         =>'required|unique:products,title,'.$product->id,
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            //check image update
            if ($request->file('image')) {
                //remove old image
                Storage::disk('local')->delete('public/products/'.basename($product->image));
                
                //upload new image
                $image = $request->file('image');
                $image->storeAs('public/products', $image->hashName());
    
                //update product with new image
                $product->update([
                    'image'         => $image->hashName(),
                    'title'         => $request->title,
                    'slug'          => Str::slug($request->title, '-'),
                    'category_id'   => $request->category_id,
                    'user_id'       => auth()->guard('api_admin')->user()->id,
                    'description'   => $request->description,
                    'weight'        => $request->weight,
                    'price'         => $request->price,
                    'stock'         => $request->stock,
                    'discount'      => $request->discount
                ]);
            }
            //update product without image
            $product->update([
                'title'         => $request->title,
                'slug'          => Str::slug($request->title, '-'),
                'category_id'   => $request->category_id,
                'user_id'       => auth()->guard('api_admin')->user()->id,
                'description'   => $request->description,
                'weight'        => $request->weight,
                'price'         => $request->price,
                'stock'         => $request->stock,
                'discount'      => $request->discount
            ]);

        if ($product) {
            //return success with Api Resource
            return new ProductResource(true, 'Data Product Berhasil Diupdate', $product);
        }
        //return failed with Api Resource
        return new ProductResource(false, 'Data Product Tidak Ditemukan', null);
    }

    /**
     * destroy
     *
     * @param  mixed $product
     * @return void
     */
    public function destroy(Product $product)
    {
        Storage::disk('local')->delete('public/products/'.basename($product->image));
        if ($product->delete()) {
            //return success with Api Resource
            return new ProductResource(true, 'Data Product Berhasil Dihapus', null);
        }
        //return failed with Api Resource
        return new ProductResource(false, 'Data Product Tidak Ditemukan', null);
    }
}
