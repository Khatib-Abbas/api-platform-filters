<?php

namespace App\ApiPlatform\Filter\Utils;

class DescriptionFilter
{
    public  function  setDescription($properties): array
    {
        $descriptions=[];
        foreach ($properties as $id =>$property) {
            $descriptions[$id] =  [
                'property'=>null,
                'type'=>'string',
                'required'=>false,
                'openapi'=>[
                    'description'=>'Search across multiple fields',
                ]
            ];
        }
        return  $descriptions;
    }
}
