<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
       //get slider 
       $sliders = Slider::latest()->paginate(5);
       //return with api resource
         return new SliderResource(true, 'List Data Slider', $sliders);
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
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        //create slider
        $slider = Slider::create([
            'image' => $image->hashName(),
            'link' => $request->link,
        ]);

        if($slider) {
            //return success with api resource
            return new SliderResource(true, 'Data Slider Berhasil Disimpan', $slider);
        }

        //return failed with api resource
        return new SliderResource(false, 'Data Slider Gagal Disimpan', null);
    }

    public function destroy(Slider $slider)
    {
        //remove image
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        if($slider->delete()) {
            //return success with api resource
            return new SliderResource(true, 'Data Slider Berhasil Dihapus', null);
        }

        //return failed with api resource
        return new SliderResource(false, 'Data Slider Gagal Dihapus', null);
    }
}
