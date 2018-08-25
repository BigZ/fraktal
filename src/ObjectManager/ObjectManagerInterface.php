<?php

namespace App\ObjectManager;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ObjectManagerInterface.
 *
 * @author Romain Richard
 */
interface ObjectManagerInterface
{
    /**
     * @param string $className
     * @param array  $sorting
     * @param array  $filterValues
     * @param array  $filerOperators
     *
     * @return array
     */
    public function getPaginatedCollection($className, array $sorting, array $filterValues, array $filerOperators);

    public function getPaginationAdapter($request);
}
