<?php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CorsListener implements EventSubscriberInterface
{
    public function __construct()
    {
        //$this->cors = $options;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST  => array('onKernelRequest', 9999),
            KernelEvents::RESPONSE => array('onKernelResponse', 9999),
        );
    }
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method  = $request->getRealMethod();
        // perform preflight checks
        if ('OPTIONS' === strtoupper($method)) {
            $response = new Response();      
            $event->setResponse($response);
            return;
        }
    }
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        // Run CORS check in here to ensure domain is in the system
        //if (in_array($request->headers->get('origin'), $this->cors)) {
        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization');
        $response->headers->set('Access-Control-Allow-Origin', '*');//$corsOrigin
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Vary', 'Origin');
        $event->setResponse($response);
        //}
        return;
    }
}