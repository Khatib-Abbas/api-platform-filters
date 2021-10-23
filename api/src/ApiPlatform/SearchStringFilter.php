<?php
namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchStringFilter implements FilterInterface
{


   public const SEARCH_STRING_FILTER_CONTEXT = 'search_string';

   public function __construct(private array $properties=[]){


   }
   public function getDescription(string $resourceClass): array
    {
        $descriptions =[];
        foreach ($this->properties as $id =>$property) {
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

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $querySearch =[];
        foreach ($this->properties as $id =>$property) {
            $search = $request->query->get($id);
            $querySearch[] = ["param" => ["attribute"=>$id ,"value"=>$search], "queryParam" => $property];
        }
        if(!$querySearch ){
            return;
        }
        $context[self::SEARCH_STRING_FILTER_CONTEXT]= $querySearch;
    }
}
