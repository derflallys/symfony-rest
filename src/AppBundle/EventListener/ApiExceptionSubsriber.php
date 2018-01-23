<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/01/18
 * Time: 01:14
 */

namespace AppBundle\EventListener;


use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubsriber implements EventSubscriberInterface
{
    private $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if(strpos($request->getPathInfo(),'/api')!==0)
        {
            return;
        }

        $e = $event->getException();
        $statusCode = $e instanceof HttpExceptionInterface? $e->getStatusCode() : 500;


        if ($statusCode == 500 && $this->debug)
        {
            return;
        }


        if ($e instanceof ApiProblemException)
        {
            $apiProblem = $e->getApiProblem();
        }
        else
        {
            $apiProblem = new ApiProblem($statusCode);
            if($e instanceof HttpExceptionInterface)
                $apiProblem->set('detail',$e->getMessage());
        }

        $data = $apiProblem->toArray();
        if($data['type']!='about:blank')
        {
            $data['type']='http://localhost:8000/docs/errors#'.$data['type'];
        }
        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type','application/problem+json');

        $event->setResponse($response);
    }
    public static function getSubscribedEvents()
    {
       return array(
           KernelEvents::EXCEPTION => 'onKernelException',
       );
    }

}