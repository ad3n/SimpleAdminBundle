<?php
namespace Ihsan\SimpleCrudBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Ihsan\SimpleCrudBundle\Event\PostFlushCrudEvent;
use Ihsan\SimpleCrudBundle\Event\PrePersistCrudEvent;
use Ihsan\SimpleCrudBundle\IhsanSimpleCrudEvents as Event;

abstract class CrudController extends Controller
{
    protected $pageTitle = 'IhsanSimpleCrudBundle';

    protected $pageDescription = 'Provide DRY CRUD for your Symfony Application';

    protected $outputParameter = array();

    protected $normalizeFilter = false;

    protected $entityClass;

    protected $formClass;

    protected $hasEventListener = false;

    /**
     * @Route("/new/")
     * @Method({"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        $entity = $this->entityClass;

        return $this->handle($request, new $entity(), 'add');
    }

    /**
     * @Route("/{id}/edit/")
     * @Method({"POST", "GET"})
     */
    public function editAction(Request $request, $id)
    {
        return $this->handle($request, $this->findOr404Error($id), 'edit');
    }

    /**
     * @Route("/{id}/show/")
     * @Method({"GET"})
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->findOr404Error($id);

        $data = array();

        foreach ($this->showFields() as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($entity, $method)) {
                array_push($data, array(
                   'name' => $property,
                    'value' => call_user_func_array(array($entity, $method), array()),
                ));
            }
        }

        return $this->render($this->container->getParameter('ihsan.simple_crud.view.show'), array(
            'data' => $data,
            'menu' => $this->container->getParameter('ihsan.simple_crud.menu'),
            'page_title' => $this->pageTitle.' | Show',
            'page_description' => $this->pageDescription,
            'back' => $request->headers->get('referer'),
        ));
    }

    /**
     * @Route("/{id}/delete/")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->findOr404Error($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }

    /**
     * @Route("/list/", name="product_category_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->entityClass);

        $qb = $repo->createQueryBuilder('o')->select('o')->addOrderBy(sprintf('o.%s', $this->container->getParameter('ihsan.simple_crud.identifier')), 'DESC');
        $filter = $this->normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            $qb->andWhere(sprintf('o.%s LIKE :filter', $this->container->getParameter('ihsan.simple_crud.filter')));
            $qb->setParameter('filter', strtr('%filter%', array('filter' => $filter)));
        }

        $page = $request->query->get('page', 1);
        $paginator  = $this->container->get('knp_paginator');

        $pagination = $paginator->paginate($qb, $page, $this->container->getParameter('ihsan.simple_crud.per_page'));

        $data = array();

        foreach ($pagination as $key => $record) {
            $temp = array();

            foreach ($this->gridFields() as $k => $property) {
                $method = 'get'.ucfirst($property);

                if (method_exists($record, $method)) {
                    array_push($temp, call_user_func_array(array($record, $method), array()));
                }
            }

            $data[$key] = $temp;
        }

        return $this->render($this->container->getParameter('ihsan.simple_crud.view.grid'),
            array(
                'pagination' => $pagination,
                'start' => ($page - 1) * $this->container->getParameter('ihsan.simple_crud.per_page'),
                'menu' => $this->container->getParameter('ihsan.simple_crud.menu'),
                'header' => array_merge($this->gridFields(), array('action')),
                'page_title' => $this->pageTitle.' | List',
                'page_description' => $this->pageDescription,
                'record' => $data,
                'filter' => $filter,
            )
        );
    }

    protected function handle(Request $request, $data, $action = 'add')
    {
        $this->outputParameter['page_title'] = $this->pageTitle. ' | '.ucfirst($action);
        $this->outputParameter['page_description'] = $this->pageDescription;

        $form = $this->getForm($data);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            if (! $form->isValid()) {

                $this->outputParameter['errors'] = true;
            } else if ($form->isValid()) {
                $entity = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $dispatcher = $this->container->get('event_dispatcher');

                $postFlushEvent = new PostFlushCrudEvent();
                $postFlushEvent->setEntityMeneger($entityManager);
                $postFlushEvent->setEntity($entity);

                $prePersistEvent = new PrePersistCrudEvent();
                $prePersistEvent->setEntityMeneger($entityManager);
                $prePersistEvent->setEntity($entity);

                if ($this->hasEventListener) {
                    $dispatcher->dispatch(Event::PRE_PERSIST_EVENT, $prePersistEvent);
                }

                $entityManager->persist($entity);
                $entityManager->flush();

                if ($this->hasEventListener) {
                    $dispatcher->dispatch(Event::POST_FLUSH_EVENT, $postFlushEvent);
                }

                $this->outputParameter['success'] = 'Data berhasil disimpan.';
            }
        }

        $this->outputParameter['form'] = $form->createView();
        $this->outputParameter['form_theme'] = $this->container->getParameter('ihsan.simple_crud.view.form_theme');
        $this->outputParameter['menu'] = $this->container->getParameter('ihsan.simple_crud.menu');

        return $this->render($this->container->getParameter('ihsan.simple_crud.view.form'), $this->outputParameter);
    }

    protected function findOr404Error($id)
    {
        $entity = $this->getDoctrine()->getManager()->getRepository($this->entityClass)->find($id);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('Data with id %s not found.', $id));
        }

        return $entity;
    }

    protected function entityProperties()
    {
        $fields = array();
        $reflection = new \ReflectionClass($this->entityClass);
        $reflection->getProperties();

        foreach ($reflection->getProperties() as $key => $property) {
            $fields[$key] = $property->getName();
        }

        return $fields;
    }

    protected function showFields()
    {
        return $this->entityProperties();
    }

    protected function gridFields()
    {
        return $this->entityProperties();
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setFormClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getFormClass()
    {
        return $this->entityClass;
    }

    public function hasEventListener($hasEventListener = true)
    {
        $this->hasEventListener = $hasEventListener;

        return $this;
    }

    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    protected function getForm($data = null)
    {
        $form = new $this->formClass();
        $form->setData($data);

        return $form;
    }
}
