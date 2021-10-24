<?php

namespace App\DataProvider\Music;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Music\Track;
use App\Repository\Music\TrackRepository;
use ApiPlatform\Core\DataProvider\Pagination;
use JsonException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class TrackDataWithCustomPaginationAndFilterProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(
        private  TrackRepository $trackRepository,
        private Pagination $pagination,
    ){

    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        [$page] = $this->pagination->getPagination($resourceClass, $operationName, $context);
        return $this->trackRepository->findLatest($page,$context);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->trackRepository->find($id);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        # TODO: remove # to activate this
        #return Track::class === $resourceClass;
    }
}
