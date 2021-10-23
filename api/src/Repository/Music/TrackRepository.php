<?php

namespace App\Repository\Music;

use App\ApiPlatform\QueryBuildFilter\QueryBuilderStringFilter\QueryBuilderStringFilter;
use App\ApiPlatform\TrackSearchFilter;
use App\DataProvider\Music\Pagination\TrackDataPaginator;
use App\Entity\Music\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private QueryBuilderStringFilter $queryBuilderStringFilter)
    {
        parent::__construct($registry, Track::class);
    }

    // /**
    //  * @return Track[] Returns an array of Track objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Track
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findLatest(int $page = 1, $context=[]): TrackDataPaginator
    {
        $createdAt_search = $context[TrackSearchFilter::SEARCH_DATE_FILTER_CONTEXT]??null;

        $qb = $this->createQueryBuilder('p')
            //->where('p.publishedAt <= :now')
            //->orderBy('p.publishedAt', 'DESC')
            //->setParameter('now', new \DateTime())
        ;
        $qbAlias=$qb->getRootAliases()[0];

        $totalItems = $this->createQueryBuilder($qbAlias)
            ->select("count($qbAlias.id)")
        ;

        $this->queryBuilderStringFilter->getQueryFilter($qb,$qbAlias,$totalItems,$context);


        if($createdAt_search){
            $date = \DateTimeImmutable::createFromFormat("Y-m-d",$createdAt_search);
            if(!$date){
                throw new BadRequestException("Invalid Date");
            }
            $qb
                ->andWhere('p.createdAt = :createdAt')
                ->setParameter('createdAt', $createdAt_search);
            $totalItems->andWhere('p.createdAt = :createdAt')
                ->setParameter('createdAt',$createdAt_search);
        }
       // dd($totalItems->getQuery());
        $finalTotalItems = $totalItems->getQuery()
        ->getSingleScalarResult();
       $paginator = new TrackDataPaginator($qb, $page, $finalTotalItems);

        return ($paginator);
    }
}
