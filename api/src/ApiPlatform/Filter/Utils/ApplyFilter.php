<?php

namespace App\ApiPlatform\Filter\Utils;

class ApplyFilter
{

    public  function  setContext($constContext, &$context, $properties, $request){
        $querySearch =[];
        foreach ($properties as $id =>$property) {
            $search = $request->query->get($id);
            $querySearch[] = ["param" => ["attribute"=>$id ,"value"=>$search], "queryParam" => $property];
        }
        if(!$querySearch ){
            return;
        }
        $context[$constContext]= $querySearch;
    }
}
