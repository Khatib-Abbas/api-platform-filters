<?php
namespace App\ApiPlatform;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class TrackSearchFilter implements FilterInterface
{


   public const SEARCH_NAME_FILTER_CONTEXT = 'name';
   public const SEARCH_DATE_FILTER_CONTEXT = 'createdAt';

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
                    'description'=>'Search across multiple fields'
                ]
            ],
            'createdAt'=>[
                'property'=>null,
                'type'=>'string',
                'required'=>false,
                'openapi'=>[
                    'description'=>'Search across multiple fields'
                ]
            ],
        ];
    }

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {

        $name = $request->query->get('name');
        $createdAt = $request->query->get('createdAt');

        if(!$name || !$createdAt && $this->throwOnValid){
            return;
        }
        $context[self::SEARCH_NAME_FILTER_CONTEXT]= $name;
        $context[self::SEARCH_DATE_FILTER_CONTEXT]= $createdAt;
    }
}
