<?php
namespace Ihsan\SimpleAdminBundle\Controller;

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
use Symfony\Component\HttpFoundation\JsonResponse;

use Ihsan\SimpleAdminBundle\Event\PostFlushCrudEvent;
use Ihsan\SimpleAdminBundle\Event\PrePersistCrudEvent;
use Ihsan\SimpleAdminBundle\IhsanSimpleCrudEvents as Event;

abstract class CrudController extends Controller
{
    protected $pageTitle = 'IhsanSimpleAdminBundle';

    protected $pageDescription = 'Provide Admin Generator with KISS Principle';

    protected $outputParameter = array();

    protected $normalizeFilter = false;

    protected $hasEventListener = false;

    protected $entityClass;

    protected $formClass;

    protected $showFields = array();

    protected $gridFields = array();

    protected $newActionTemplate = 'IhsanSimpleAdminBundle:Crud:new.html.twig';

    protected $editActionTemplate = 'IhsanSimpleAdminBundle:Crud:new.html.twig';

    protected $showActionTemplate = 'IhsanSimpleAdminBundle:Crud:show.html.twig';

    protected $listActionTemplate = 'IhsanSimpleAdminBundle:Crud:list.html.twig';

    /**
     * @Route("/new/")
     * @Method({"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        $entity = $this->entityClass;

        return $this->handle($request, new $entity(), $this->newActionTemplate, 'new');
    }

    /**
     * @Route("/{id}/edit/")
     * @Method({"POST", "GET"})
     */
    public function editAction(Request $request, $id)
    {
        return $this->handle($request, $this->findOr404Error($id), $this->editActionTemplate, 'edit');
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

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        return $this->render($this->showActionTemplate, array(
            'data' => $data,
            'menu' => $this->container->getParameter('ihsan.simple_admin.menu'),
            'page_title' => 'Show'.' '.$translator->trans($this->pageTitle, array(), $translationDomain),
            'page_description' => $translator->trans($this->pageDescription, array(), $translationDomain),
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

        return new JsonResponse(array('status' => true));
    }

    /**
     * @Route("/list/")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->entityClass);

        $qb = $repo->createQueryBuilder('o')->select('o')->addOrderBy(sprintf('o.%s', $this->container->getParameter('ihsan.simple_admin.identifier')), 'DESC');
        $filter = $this->normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            $qb->andWhere(sprintf('o.%s LIKE :filter', $this->container->getParameter('ihsan.simple_admin.filter')));
            $qb->setParameter('filter', strtr('%filter%', array('filter' => $filter)));
        }

        $page = $request->query->get('page', 1);
        $paginator  = $this->container->get('knp_paginator');

        $pagination = $paginator->paginate($qb, $page, $this->container->getParameter('ihsan.simple_admin.per_page'));

        $data = array();
        $identifier = array();

        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($this->gridFields() as $k => $property) {
                $method = 'get'.ucfirst($property);

                if (method_exists($record, $method)) {
                    array_push($temp, call_user_func_array(array($record, $method), array()));
                }
            }

            $data[$key] = $temp;
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        return $this->render($this->listActionTemplate,
            array(
                'pagination' => $pagination,
                'start' => ($page - 1) * $this->container->getParameter('ihsan.simple_admin.per_page'),
                'menu' => $this->container->getParameter('ihsan.simple_admin.menu'),
                'header' => array_merge($this->gridFields(), array('action')),
                'page_title' => 'List '.$translator->trans($this->pageTitle, array(), $translationDomain),
                'page_description' => $translator->trans($this->pageDescription, array(), $translationDomain),
                'identifier' => $identifier,
                'record' => $data,
                'filter' => $filter,
            )
        );
    }

    protected function handle(Request $request, $data, $template, $action = 'new')
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        $this->outputParameter['page_title'] = ucfirst($action).' '.$translator->trans($this->pageTitle, array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans($this->pageDescription, array(), $translationDomain);

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

                $this->outputParameter['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        $this->outputParameter['form'] = $form->createView();
        $this->outputParameter['form_theme'] = $this->container->getParameter('ihsan.simple_admin.themes.form_theme');
        $this->outputParameter['menu'] = $this->container->getParameter('ihsan.simple_admin.menu');

        return $this->render($template, $this->outputParameter);
    }

    protected function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        $entity = $this->getDoctrine()->getManager()->getRepository($this->entityClass)->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%', $id), $translationDomain));
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
        if (! empty($this->showFields)) {

            return $this->showFields;
        }

        return $this->entityProperties();
    }

    protected function gridFields()
    {
        if (! empty($this->gridFields)) {

            return $this->gridFields;
        }

        return $this->entityProperties();
    }

    /**
     * @param string $entityClass
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param string $formClass
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;

        return $this;
    }

    /**
     * @param boolean $hasEventListener
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function hasEventListener($hasEventListener = true)
    {
        $this->hasEventListener = $hasEventListener;

        return $this;
    }

    /**
     * @param boolean $normalizeFilter
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function normalizeFilter($normalizeFilter = true)
    {
        $this->normalizeFilter = $normalizeFilter;

        return $this;
    }

    /**
     * @param string $pageTitle
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * @param string $pageDescription
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    /**
     * @param array $fields
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setShowFields(array $fields)
    {
        $this->showFields = $fields;

        return $this;
    }

    /**
     * @param array $fields
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setGridFields(array $fields)
    {
        $this->gridFields = $fields;

        return $this;
    }

    protected function getForm($data = null)
    {
        try {
            $formObject = $this->container->get($this->formClass);
        } catch (\Exception $ex) {
            $formObject = new $this->formClass();
        }

        $form = $this->createForm($formObject);
        $form->setData($data);

        return $form;
    }

    /**
     * @param string $template
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setNewActionTemplate($template)
    {
        $this->newActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setEditActionTemplate($template)
    {
        $this->editActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setShowActioinTemplate($template)
    {
        $this->showActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setListActionTemplate($template)
    {
        $this->listActionTemplate = $template;

        return $this;
    }
}
