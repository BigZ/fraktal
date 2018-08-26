<?php

namespace App\Subscriber;

use League\Fractal\Resource\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use WizardsRest\WizardsRest;

class SerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var WizardsRest
     */
    private $rest;

    /**
     * @var DiactorosFactory
     */
    private $psrFactory;

    /**
     * SerializationSubscriber constructor.
     *
     * @param WizardsRest $rest
     */
    public function __construct(WizardsRest $rest)
    {
        $this->rest = $rest;
        $this->psrFactory = new DiactorosFactory();
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $this->psrFactory->createRequest($event->getRequest());
        $resource =  $this->rest->transform($event->getControllerResult(), $request);

        // Add pagination if resource is a collecion
        if ($resource instanceof Collection) {
            $resource->setPaginator($this->rest->getPaginationAdapter($request));
        }

        $response = new Response(
            $this->rest->serialize($resource, WizardsRest::SPEC_JSONAPI, WizardsRest::FORMAT_JSON),
            200,
            ['Content-Type' => 'application/vnd.api+json']
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onKernelView'
        ];
    }
}
