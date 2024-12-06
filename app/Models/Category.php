<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'image'];

    /**
     * products
     *
     * @return void
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * getImageAtribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAtribute( $image)
    {
        return asset('storage/categories/' . $image);
    }
}
