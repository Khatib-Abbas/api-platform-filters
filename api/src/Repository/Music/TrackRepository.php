<?php

namespace App\Repository\Music;

use App\ApiPlatform\SearchStringFilter;
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
    public function __construct(ManagerRegistry $registry)
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

        $totalItems = $this->createQueryBuilder('p')
            ->select('count(p.id)')
        ;
        $search = $context[SearchStringFilter::SEARCH_STRING_FILTER_CONTEXT]??null;
        if($search){
            $queryString = "";
            $cpt=0;
            foreach ($search as $property) {
                $param = $property["param"]["attribute"];
                $values =$property["param"]["value"];
                $queryParam =$property["queryParam"];
                if($param && $values && in_array($queryParam,["start","partial","exact","end","Le","_uy","%are_","_are%"],true) ){
                    if(is_array($values)){
                        foreach ($values as $id=>$value) {
                           if($cpt===0){
                               $queryString .= "p.$param LIKE :a$id";

                           }else{
                               $queryString .= " OR p.$param LIKE :a$id";
                           }
                           switch ($queryParam){
                               case "start":
                                   #Begins with Kim
                                   $qb->setParameter("a$id","$value%");
                                   $totalItems->setParameter("a$id","$value%");
                                   break;
                               case "partial":
                                   #Contains ch
                                   $qb->setParameter("a$id","%$value%");
                                   $totalItems->setParameter("a$id","%$value%");
                                   break;
                               case "exact":
                                   #Contains ch
                                   $qb->setParameter("a$id","$value");
                                   $totalItems->setParameter("a$id","$value");
                                   break;
                               case "end":
                                   #Ends with er
                                   $qb->setParameter("a$id","%$value");
                                   $totalItems->setParameter("a$id","%$value");
                                   break;
                               case "Le":
                                   #"Begins with Le and is followed by at most one character e.g., Les, Len…
                                   $qb->setParameter("a$id",$value."_");
                                   $totalItems->setParameter("a$id",$value."_");
                                   break;
                               case "_uy":
                                   #Ends with uy and is preceded by at most one character e.g., guy
                                   $qb->setParameter("a$id","_".$value);
                                   $totalItems->setParameter("a$id","_".$value);
                                   break;
                               case "%are_":
                                   #Contains are, begins with any number of characters and ends with at most one character
                                   $qb->setParameter("a$id","%".$value."_");
                                   $totalItems->setParameter("a$id","%".$value."_");
                                   break;
                               case "_are%":
                                   #Contains are, begins with at most one character and ends with any number of characters
                                   $qb->setParameter("a$id","_".$value."%");
                                   $totalItems->setParameter("a$id","_".$value."%");
                                   break;
                           }
                           $cpt++;
                       }

                   }
                   else{
                       $qb->andWhere("p.$param = :$param")->setParameter($param,$values);
                       $totalItems->andWhere("p.$param = :$param")->setParameter($param,$values);
                   }

                }
            }
            if($queryString){
                $qb->andWhere($queryString);
                $totalItems->andWhere($queryString);
            }
        }

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
