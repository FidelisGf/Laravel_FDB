<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       
        //return parent::toArray($request);
        return [
            'ID' => $this->ID_PRODUTO,
            'NOOME' => $this->NOME,
            'DESC' => $this->DESC,
            'VALOR' => $this->VALOR
        ];
    }
}
