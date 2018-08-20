<?php

namespace App\Service;

use App\Transformer\EntityTransformer;
use Symfony\Component\HttpFoundation\Request;
use League\Fractal;
use League\Fractal\Manager;

class Fraktal
{
    private $defaultTransformer;

    private $manager;

    public function __construct(EntityTransformer $defaultTransformer)
    {
        $this->defaultTransformer = $defaultTransformer;
        $this->manager = new Manager();
    }

    public function transform($entity, Request $request, Fractal\TransformerAbstract $userTransformer = null)
    {
        $transformer = null === $userTransformer ? $this->getDefaultTransformer($request) : $userTransformer;

        return new Fractal\Resource\Item($entity, $transformer);
    }

    public function serialize($resource)
    {
        return $this->manager->createData($resource)->toJson();
    }

    private function getDefaultTransformer(Request $request)
    {
        $this->defaultTransformer->parseRequest($request);

        return $this->defaultTransformer;
    }
}