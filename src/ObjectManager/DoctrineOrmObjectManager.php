<?php

namespace App\ObjectManager;

use Doctrine\Common\Persistence\ObjectManager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * Interface ObjectManagerInterface.
 *
 * @author Romain Richard
 */
class DoctrineOrmObjectManager implements ObjectManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Pagerfanta
     */
    private $paginator;

    /**
     * @var Router
     */
    private $router;

    /**
     * DoctrineOrmObjectManager constructor.
     * @param ObjectManager $objectManager
     * @param Router $router
     */
    public function __construct(ObjectManager $objectManager, RouterInterface $router)
    {
        $this->objectManager = $objectManager;
        $this->router = $router;
        $this->paninator = null;
    }


    public function getPaginatedCollection($className, array $sorting = [], array $filterValues = [], array $filerOperators = [])
    {
        $doctrineAdapter = new DoctrineORMAdapter(
            $this->findAllSorted($className, $sorting, $filterValues, $filerOperators)
        );
        $this->paginator = new Pagerfanta($doctrineAdapter);

        return $this->paginator->getCurrentPageResults();
    }

    public function getPaginationAdapter($request)
    {
        $router = $this->router;
        return new PagerfantaPaginatorAdapter(
            $this->paginator,
            function(int $page) use ($request, $router) {
                $route = $request->attributes->get('_route');
                $inputParams = $request->attributes->get('_route_params');
                $newParams = array_merge($inputParams, $request->query->all());
                $newParams['page'] = $page;
                return $router->generate($route, $newParams, 0);
            });
    }

    /**
     * @param string $className
     * @param array  $sorting
     * @param array  $filterValues
     * @param array  $filerOperators
     *
     * @return QueryBuilder
     */
    private function findAllSorted($className, array $sorting = [], array $filterValues = [], array $filerOperators = [])
    {
        $fields = array_keys($this->objectManager->getClassMetadata($className)->fieldMappings);
        $repository = $this->objectManager->getRepository($className);

        // If user's own implementation is defined, use it
        try {
            return $repository->findAllSorted($sorting, $filterValues, $filerOperators);
        } catch (\BadMethodCallException $exception) {
            $queryBuilder = $repository->createQueryBuilder('e');

            foreach ($sorting as $name => $direction) {
                if (in_array($name, $fields)) {
                    $queryBuilder->addOrderBy('e.' . $name, $direction);
                }
            }

            foreach ($fields as $field) {
                if (isset($filterValues[$field])) {
                    $operator = '=';

                    if (isset($filerOperators[$field])
                        && in_array($filerOperators[$field], ['>', '<', '>=', '<=', '=', '!='])
                    ) {
                        $operator = $filerOperators[$field];
                    }

                    $queryBuilder->andWhere('e.'.$field.$operator."'".$filterValues[$field]."'");
                }
            }

            return $queryBuilder;
        }
    }
}