<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use AppBundle\Form\ProgrammerType;
use AppBundle\Form\UpdateProgrammerType;
use AppBundle\Pagination\PaginationCollection;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProgrammerController extends BaseController
{
    /**
     * @Route("/api/programmers")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $progammer = new Programmer();


        $form = $this->createForm(new ProgrammerType(),$progammer);
        $this->processForm($request,$form);
        if(!$form->isValid())
        {
            return $this->throwApiProblemValidationException($form);
        }
        $progammer->setUser($this->findUserByUsername('weaverryan'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($progammer);
        $em->flush();

        $location = $this->generateUrl('api_programmers_show',[
            'nickname' =>$progammer->getNickname()
        ]);

        $response = $this->createApiResponse($progammer,201);
        $response->headers->set('Location',$location);

        return $response;
    }

    /**
     * @Route("/api/programmers/{nickname}",name="api_programmers_show")
     * @Method("GET")
     */
    public function showAction($nickname)
    {
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if(!$programmer)
            throw $this->createNotFoundException('No programmer found for username '.$nickname);

        $response = $this->createApiResponse($programmer);
        return $response;
    }

    /**
     * @Route("/api/programmers",name="api_programmers_collection")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findAllQueryBuilder($filter);

        $paginationCollection = $this->get('pagination_factory')
        ->createCollection($qb,$request,'api_programmers_collection');
        $response = $this->createApiResponse($paginationCollection);
        return $response;
        
    }


    private function processForm(Request $request,FormInterface $formInteface)
    {
        $body = $request->getContent();
        $data = json_decode($body,true);
        if(null=== $data)
        {
            $apiProblem = new ApiProblem(400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);

            throw new ApiProblemException($apiProblem);

        }
        $clearMissing = $request->getMethod() !='PATCH';

        $formInteface->submit($data,$clearMissing);
    }

    /**
     * @Route("/api/programmers/{nickname}",name="api_programmers_update")
     * @Method({"PUT","PATCH"})
     */
    public function updateAction($nickname,Request $request)
    {
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if(!$programmer)
            throw $this->createNotFoundException(' No programmer found for username'.$nickname);


        $form = $this->createForm(new UpdateProgrammerType(),$programmer);
        $this->processForm($request,$form);
        if(!$form->isValid())
        {
           return $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();
        $response = $this->createApiResponse($programmer);


        return $response;
    }
    /**
     * @Route("/api/programmers/{nickname}")
     * @Method("DELETE")
     */
    public function deleteAction($nickname)
    {
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if($programmer)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($programmer);
            $em->flush();
        }

        return new Response(null,204);


    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();

        }


        foreach ($form->all() as $childForm) {

            if ($childForm instanceof FormInterface) {

                if ($childErrors = $this->getErrorsFromForm($childForm)) {

                    $errors[$childForm->getName()] = $childErrors;

                }

            }

        }


        return $errors;
    }

    private function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR);
        $apiProblem->set('errors',$errors);
        throw new ApiProblemException($apiProblem);
    }


}
