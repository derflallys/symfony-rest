<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/01/18
 * Time: 00:41
 */

namespace AppBundle\Serializer;


use AppBundle\Annotation\Link;
use AppBundle\Entity\Programmer;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\RouterInterface;

class LinkSerializationSubscriber implements EventSubscriberInterface
{

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Reader
     */
    private $reader;

    private $expressionLanguage;

    public function __construct(RouterInterface $router,Reader $reader)
    {

        $this->router = $router;
        $this->reader = $reader;
        $this->expressionLanguage = new ExpressionLanguage();
    }


    public static function getSubscribedEvents()
    {
      return   array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json'
            )

         );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /**
         * @var JsonSerializationVisitor $visitor
         */
        $visitor = $event->getVisitor();

        $object = $event->getObject();
        $annotations = $this->reader
            ->getClassAnnotations(new \ReflectionObject($object));
        $links = array();
        foreach ($annotations as $annotation)
        {
            if($annotation instanceof  Link)
            {
                $uri =  $this->router->generate(
                    $annotation->route,
                    $this->resolveParams($annotation->params,$object)
                );

                $links[$annotation->name]=$uri;
            }

        }
        if($links)
            $visitor->addData('_links',$links);
    }

    private function resolveParams(array  $params,$object)
    {
        foreach ($params as $key => $param)
        {
            $params[$key] = $this->expressionLanguage
                ->evaluate($param,array('object' => $object));
        }

        return $params;
    }
}