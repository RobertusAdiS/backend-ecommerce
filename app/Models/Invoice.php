<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'invoice',
        'customer_id',
        'courier',
        'courier_service',
        'courier_cost',
        'weight',
        'nama',
        'phone',
        'city_id',
        'province_id',
        'address',
        'status',
        'grand_total',
        'snap_token'
    ];
    
    /**
     * orders
     *
     * @return void
     */
    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * customer
     *
     * @return void
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * city
     *
     * @return void
     */
    public function city()  
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    /**
     * province
     *
     * @return void
     */
    public function province()  
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }
}
