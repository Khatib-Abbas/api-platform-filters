<?php
namespace App\ApiPlatform\Filter\DateFilter;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use App\ApiPlatform\Filter\Utils\ApplyFilter;
use Symfony\Component\HttpFoundation\Request;

class DateFilter implements FilterInterface
{


   public const SEARCH_DATE_FILTER_CONTEXT = 'date';
    public function __construct(private ApplyFilter $applyFilter,private array $properties=[]){
    }
    public function getDescription(string $resourceClass): array
    {
        return  [
            'name'=>[
                'property'=>null,
                'type'=>'string',
                'required'=>false,
                'openapi'=>[
                    'description'=>'Date across multiple fields'
                ]
            ],
        ];
    }

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $this->applyFilter->setContext(self::SEARCH_DATE_FILTER_CONTEXT,$context,$this->properties,$request);
    }
}
