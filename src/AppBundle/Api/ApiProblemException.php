<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/01/18
 * Time: 00:37
 */

namespace AppBundle\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private $apiProblem;
    public function __construct(ApiProblem $apiProblem,\Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem =  $apiProblem;
        $statusCode = $this->apiProblem->getStatusCode();
        $message = $this->apiProblem->getTitle();
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem()
    {
        return $this->apiProblem;
    }

}