<?php
namespace Ihsan\SimpleAdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ihsan\SimpleAdminBundle\Event\PreFormCreateEvent;
use Ihsan\SimpleAdminBundle\Event\PreFormValidationEvent;
use Ihsan\SimpleAdminBundle\Event\PreSaveEvent;
use Ihsan\SimpleAdminBundle\Event\PostSaveEvent;
use Ihsan\SimpleAdminBundle\Event\FilterListEvent;
use Ihsan\SimpleAdminBundle\Event\PreDeleteEvent;
use Ihsan\SimpleAdminBundle\IhsanSimpleAdminEvents as Event;

abstract class CrudController extends AbstractController implements OverridableTemplateInterface
{
    protected $outputParameter = array();

    protected $normalizeFilter = false;

    protected $gridFields = array();

    protected $newActionTemplate = 'IhsanSimpleAdminBundle:Crud:new.html.twig';

    protected $editActionTemplate = 'IhsanSimpleAdminBundle:Crud:new.html.twig';

    protected $showActionTemplate = 'IhsanSimpleAdminBundle:Crud:show.html.twig';

    protected $listActionTemplate = 'IhsanSimpleAdminBundle:Crud:list.html.twig';

    const ENTITY_ALIAS = 'o';

    /**
     * @Route("/new/")
     * @Method({"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        $event = new PreFormCreateEvent();
        $event->setController($this);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(Event::PRE_FORM_CREATE_EVENT, $event);

        $entity = $event->getFormData();

        if (! $entity) {
            $entity = new $this->entityClass();
        }

        return $this->handle($request, $entity, $this->newActionTemplate, 'new');
    }

    /**
     * @Route("/{id}/edit/")
     * @Method({"POST", "GET"})
     */
    public function editAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('edit');

        $event = new PreFormCreateEvent();
        $event->setController($this);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(Event::PRE_FORM_CREATE_EVENT, $event);

        $entity = $event->getFormData();

        if (! $entity) {
            $entity = $this->findOr404Error($id);
        }

        return $this->handle($request, $entity, $this->editActionTemplate, 'edit');
    }

    /**
     * @Route("/{id}/show/")
     * @Method({"GET"})
     */
    public function showAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('show');

        $entity = $this->findOr404Error($id);

        $data = array();

        foreach ($this->showFields() as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($entity, $method)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($entity, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($entity, $method)) {
                    array_push($data, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($entity, $method), array()),
                    ));
                }
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
            'action' => $this->container->getParameter('ihsan.simple_admin.grid_action'),
        ));
    }

    /**
     * @Route("/{id}/delete/")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('delete');
        $entity = $this->findOr404Error($id);
        $entityManager = $this->getDoctrine()->getManager();

        $event = new PreDeleteEvent();
        $event->setEntity($entity);
        $event->setEntityMeneger($entityManager);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(Event::PRE_DELETE_EVENT, $event);

        if ($event->getResponse()) {

            return $event->getResponse();
        }

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

        $qb = $repo->createQueryBuilder(self::ENTITY_ALIAS)->select(self::ENTITY_ALIAS)->addOrderBy(sprintf('%s.%s', self::ENTITY_ALIAS, $this->container->getParameter('ihsan.simple_admin.identifier')), 'DESC');
        $filter = $this->normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            $qb->andWhere(sprintf('%s.%s LIKE :filter', self::ENTITY_ALIAS, $this->container->getParameter('ihsan.simple_admin.filter')));
            $qb->setParameter('filter', strtr('%filter%', array('filter' => $filter)));
        }

        $event = new FilterListEvent();
        $event->setQueryBuilder($qb);
        $event->setEntityAlias(self::ENTITY_ALIAS);
        $event->setEntityClass($this->entityClass);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(Event::FILTER_LIST_EVENT, $event);

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
                } else {
                    $method = 'is'.ucfirst($property);

                    if (method_exists($record, $method)) {
                        array_push($temp, call_user_func_array(array($record, $method), array()));
                    }
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
                'action' => $this->container->getParameter('ihsan.simple_admin.grid_action'),
                'record' => $data,
                'filter' => $filter,
            )
        );
    }

    protected function handle(Request $request, $data, $template, $action = 'new')
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        $form = $this->getForm($data);
        $form->handleRequest($request);

        $this->outputParameter['page_title'] = ucfirst($action).' '.$translator->trans($this->pageTitle, array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans($this->pageDescription, array(), $translationDomain);
        $this->outputParameter['form'] = $form->createView();
        $this->outputParameter['form_theme'] = $this->container->getParameter('ihsan.simple_admin.themes.form_theme');
        $this->outputParameter['menu'] = $this->container->getParameter('ihsan.simple_admin.menu');

        $dispatcher = $this->container->get('event_dispatcher');

        if ($request->isMethod('POST')) {
            $preFormValidationEvent = new PreFormValidationEvent();
            $preFormValidationEvent->setRequest($request);

            $dispatcher->dispatch(Event::PRE_FORM_VALIDATION_EVENT, $preFormValidationEvent);

            if (! $form->isValid()) {

                $this->outputParameter['errors'] = true;
            } else if ($form->isValid()) {
                $entity = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();

                $preSaveEvent = new PreSaveEvent();
                $preSaveEvent->setRequest($request);
                $preSaveEvent->setEntity($entity);
                $preSaveEvent->setEntityMeneger($entityManager);

                $postSaveEvent = new PostSaveEvent();
                $postSaveEvent->setEntityMeneger($entityManager);
                $postSaveEvent->setEntity($entity);

                $dispatcher->dispatch(Event::PRE_SAVE_EVENT, $preSaveEvent);

                $entityManager->persist($entity);
                $entityManager->flush();

                $dispatcher->dispatch(Event::POST_SAVE_EVENT, $postSaveEvent);

                $this->outputParameter['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        return $this->render($template, $this->outputParameter);
    }

    protected function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        $entity = $this->getDoctrine()->getManager()->getRepository($this->entityClass)->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%' => $id), $translationDomain));
        }

        return $entity;
    }

    protected function isAllowedOr404Error($action)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        if (! in_array($action, $this->container->getParameter('ihsan.simple_admin.grid_action'))) {
            throw new NotFoundHttpException($translator->trans('message.request_not_found', array(), $translationDomain));
        }

        return true;
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
     * @param boolean $normalizeFilter
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function normalizeFilter($normalizeFilter = true)
    {
        $this->normalizeFilter = $normalizeFilter;

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

    /**
     * @return boolean
     */
    public function allowOverrideTemplate()
    {
        return true;
    }
}
