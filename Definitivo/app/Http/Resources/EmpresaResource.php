<?php

namespace App\Http\Resources;

use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'ID' => $this->ID_EMPRESA,
            'NOME' => $this->NOME,
            'CNPJ' => $this->CNPJ,
            'CATEGORYS' => CategoryResource::collection($this->category),
        ];
    }
}
