<?php
namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DateFilter implements FilterInterface
{


   public const SEARCH_NAME_FILTER_CONTEXT = 'date';

   public function __construct(private bool $throwOnValid =false){


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
        $date = $request->query->get('date');
        if(!$date  && $this->throwOnValid){
            return;
        }
        $context[self::SEARCH_NAME_FILTER_CONTEXT]= $date;
    }
}
