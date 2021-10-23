<?php

namespace App\ApiPlatform\QueryBuildFilter\QueryBuilderStringFilter;

use App\ApiPlatform\Filter\StringFilter\SearchStringFilter;

class QueryBuilderStringFilter
{

    const QUERY_PARAM_LIKE= ["start","partial","exact","end","Le","_uy","%are_","_are%"];
    public  function getQueryFilter($qb,$qbAlias,$totalItems,$context){
        $search = $context[SearchStringFilter::SEARCH_STRING_FILTER_CONTEXT]??null;
        if($search){
            $queryString = "";
            $cpt=0;
            foreach ($search as $property) {
                $param = $property["param"]["attribute"];
                $values =$property["param"]["value"];
                $queryParam =$property["queryParam"];
                if($param && $values && in_array($queryParam,self::QUERY_PARAM_LIKE,true) ){
                    if(is_array($values)){
                        foreach ($values as $id=>$value) {
                            if($cpt===0){
                                $queryString .= "$qbAlias.$param LIKE :a$id";

                            }else{
                                $queryString .= " OR $qbAlias.$param LIKE :a$id";
                            }
                            $this->chooseLike($queryParam,$totalItems,$qb,$id,$value);
                            $cpt++;
                        }

                    }
                    else{
                        $qb->andWhere("$qbAlias.$param = :$param")->setParameter($param,$values);
                        $totalItems->andWhere("$qbAlias.$param = :$param")->setParameter($param,$values);
                    }

                }
            }
            if($queryString){
                $qb->andWhere($queryString);
                $totalItems->andWhere($queryString);
            }
        }
    }

    public function chooseLike($queryParam,$totalItems,$qb,$id,$value){
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
            # TODO : SYSYEME ME DIT FALSE ICI JE COMPREND PAS
            case "_are%":
                #Contains are, begins with at most one character and ends with any number of characters
                $qb->setParameter("a$id","_".$value."%");
                $totalItems->setParameter("a$id","_".$value."%");
                break;
        }
    }
}
