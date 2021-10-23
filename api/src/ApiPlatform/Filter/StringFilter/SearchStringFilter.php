<?php
namespace App\ApiPlatform\Filter\StringFilter;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use App\ApiPlatform\Filter\Utils\ApplyFilter;
use Symfony\Component\HttpFoundation\Request;

class SearchStringFilter implements FilterInterface
{


   public const SEARCH_STRING_FILTER_CONTEXT = 'search_string';

   public function __construct(private ApplyFilter $applyFilter,private array $properties=[]){
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
        $this->applyFilter->setContext(self::SEARCH_STRING_FILTER_CONTEXT,$context,$this->properties,$request);
    }
}
