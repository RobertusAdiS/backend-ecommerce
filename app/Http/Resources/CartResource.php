<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    //public properties
    public $status;
    public $message;

    public function __construct($status, $message, $resource)
    {
        $this->status = $status;
        $this->message = $message;
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return response
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->resource
        ];
    }
}
