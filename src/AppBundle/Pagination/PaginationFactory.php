<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 21/01/18
 * Time: 01:11
 */

namespace AppBundle\Pagination;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * PaginationFactory constructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $qb, Request $request, $route, array $routeParams = array())
    {
        $page = $request->query->get('page',1);



        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        $programmers = array();
        foreach ($pagerfanta->getCurrentPageResults() as $programmer)
        {
            $programmers[] = $programmer;
        }
        $paginationCollection = new PaginationCollection($programmers,$pagerfanta->getNbResults());


        $routeParams = array_merge($routeParams,$request->query->all());

        $createLinkUrl = function ($targetPage) use ($route,$routeParams){
            return $this->router->generate($route,array_merge($routeParams,
                array('page' =>$targetPage)
            ));
        };

        $paginationCollection->addLink('self',$createLinkUrl($page));
        $paginationCollection->addLink('first',$createLinkUrl(1));
        $paginationCollection->addLink('last',$createLinkUrl($pagerfanta->getNbPages()));

        if($pagerfanta->hasNextPage()){
            $paginationCollection->addLink('next',$createLinkUrl($pagerfanta->getNextPage()));
        }

        if($pagerfanta->hasPreviousPage()){
            $paginationCollection->addLink('prev',$createLinkUrl($pagerfanta->getPreviousPage()));
        }
        return $paginationCollection;
    }
}