<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/01/18
 * Time: 01:47
 */

namespace AppBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */

class Link
{
    /**
     * @Required
     */
    public $route;

    /**
     * @Required
     */
    public $name;

    public $params =array() ;
}