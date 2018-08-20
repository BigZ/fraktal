<?php

namespace App\Service;

use App\Transformer\EntityTransformer;
use Symfony\Component\HttpFoundation\Request;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\ResourceInterface;

class Fraktal
{
    const SPEC_JSONAPI = 'SPEC_JSONAPI';
    const SPEC_ARRAY = 'SPEC_ARRAY';
    const SPEC_DATA_ARRAY = 'SPEC_DATA_ARRAY';

    const FORMAT_JSON = 'FORMAT_JSON';
    const FORMAT_ARRAY = 'FORMAT_ARRAY';

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

        return new Fractal\Resource\Item(
            $entity,
            $transformer,
            strtolower((new \ReflectionClass($entity))->getShortName())
        );
    }

    public function serialize(
        ResourceInterface $resource,
        $specification = self::SPEC_DATA_ARRAY,
        $format = self::FORMAT_ARRAY
    ) {
        switch ($specification) {
            case self::SPEC_JSONAPI:
                $baseUrl = 'http://example.com';
                $this->manager->setSerializer(new JsonApiSerializer($baseUrl));
                break;
            case self::SPEC_ARRAY:
                $this->manager->setSerializer(new ArraySerializer());
                break;
        }


        switch ($format) {
            case self::FORMAT_JSON:
                return $this->manager->createData($resource)->toJson();
        }

        return $this->manager->createData($resource)->toArray();
    }

    private function getDefaultTransformer(Request $request)
    {
        $this->defaultTransformer->parseRequest($request);

        return $this->defaultTransformer;
    }
}