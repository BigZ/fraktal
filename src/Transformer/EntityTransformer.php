<?php

namespace App\Transformer;

use App\ObjectReader\ObjectReaderInterface;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Request;

class EntityTransformer extends TransformerAbstract
{
    private $includes;

    private $fields;

    /**
     * @var ObjectReaderInterface
     */
    private $objectReader;

    /**
     * EntityTransformer constructor.
     *
     * @param ObjectReaderInterface $objectReader
     */
    public function __construct(ObjectReaderInterface $objectReader)
    {
        $this->objectReader = $objectReader;
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * @param $resource
     * @return mixed
     * @throws \ReflectionException
     */
    public function transform($resource)
    {
        return $this->objectReader->getExposedProperties($resource, $this->fields);
    }

    public function parseRequest(Request $request)
    {
        $this->includes = $this->getComaSeparatedQueryParams($request, 'include');
        $this->fields = $this->getComaSeparatedQueryParams($request, 'fields');
    }

    /**
     * Get the embed query param.
     *
     * @return array
     */
    private function getComaSeparatedQueryParams(Request $request, $name)
    {
        $include = $request->query->get($name);

        return $include ? explode(',', $include) : [];
    }

    /**
     * Include Author
    public function includeAuthor($resource)
    {
        $author = $resource->author;

        return $this->item($author, $this);
    }*/
}