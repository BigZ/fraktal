<?php

namespace App\ObjectReader;

use App\Annotation\Embeddable;
use App\Annotation\Exposable;
use Doctrine\Common\Annotations\Reader;

/**
 * Reads annotations.
 *
 * @author Romain Richard
 */
class DoctrineAnnotationReader implements ObjectReaderInterface
{
    /**
     * @var Reader
     */
    protected $annotationReader;

    /**
     * DoctrineAnnotationReader constructor.
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Get the route name of a relationship.
     *
     * @param \ReflectionProperty $property
     * @param string              $targetClass
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function getAssociationRouteName(\ReflectionProperty $property, $targetClass)
    {
        /**
         * @var Embeddable
         */
        $annotation = $this->annotationReader->getPropertyAnnotation($property, Embeddable::class);

        if (null !== $annotation && $annotation->getRouteName()) {
            return $annotation->getRouteName();
        }

        return $this->getResourceRouteName(new \ReflectionClass($targetClass));
    }

    /**
     * Return the configured route name for a resource, or get_*entityShortName* by default.
     *
     * @param \ReflectionClass $resource
     *
     * @return string
     */
    public function getResourceRouteName(\ReflectionClass $resource)
    {
        /**
         * @var Embeddable
         */
        $annotation = $this->annotationReader->getClassAnnotation($resource, Embeddable::class);

        if (null !== $annotation && $annotation->getRouteName()) {
            return $annotation->getRouteName();
        }

        return sprintf('get_%s', strtolower($resource->getShortName()));
    }

    /**
     * Return the configured route name for a resource collection, or get_*entityShortName*s by default.
     *
     * @param \ReflectionClass $resource
     *
     * @return string
     */
    public function getResourceCollectionRouteName(\ReflectionClass $resource)
    {
        /**
         * @var Embeddable
         */
        $annotation = $this->annotationReader->getClassAnnotation($resource, Embeddable::class);

        if (null !== $annotation && $annotation->getCollectionRouteName()) {
            return $annotation->getCollectionRouteName();
        }

        return sprintf('get_%ss', strtolower($resource->getShortName()));
    }

    /**
     * Does an entity's property have the @embeddable annotation ?
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    public function isPropertyEmbeddable(\ReflectionProperty $property)
    {
        return null !== $this->annotationReader->getPropertyAnnotation($property, Embeddable::class);
    }

    /**
     * Does an entity's property have the @exposable annotation ?
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    public function isPropertyExposable(\ReflectionProperty $property)
    {
        return null !== $this->annotationReader->getPropertyAnnotation($property, Exposable::class);
    }

    public function getExposedProperties($resource, array $filter)
    {
        // @TODO we want to have different possible strategies faut exposing properties
        // to include everything or nothing by default (or maybe scalars only ?)
        // as well has having multiple filtering strategies (fields=name,date...) such as all or nothing
        $propertyList = [];
        $reflectionClass = new \ReflectionClass($resource);
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            if (
                $this->isPropertyExposable($property) &&
                (count($filter) > 0 ? in_array($propertyName, $filter) : true)
            ) {
                $propertyList[$propertyName] = $resource->{$this->getProperyGetter($property)}();
            }
        }

        return $propertyList;
    }

    private function getProperyGetter(\ReflectionProperty $property)
    {
        $exposable = $this->annotationReader->getPropertyAnnotation($property, Exposable::class);

        if (null !== $exposable->getGetter()) {
            return $exposable->getGetter();
        }

        return 'get'.ucfirst($property->getName());
    }
}
