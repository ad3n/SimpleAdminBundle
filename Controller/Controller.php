<?php
namespace Ihsan\SimpleAdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected $pageTitle = 'IhsanSimpleAdminBundle';

    protected $pageDescription = 'Provide Admin Generator with KISS Principle';

    protected $showFields = array();

    protected $entityClass;

    protected $formClass;

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

    /**
     * @param array $fields
     * @return \Ihsan\SimpleAdminBundle\Controller\AbstractController
     */
    public function setShowFields(array $fields)
    {
        $this->showFields = $fields;

        return $this;
    }

    /**
     * @param string $pageTitle
     * @return \Ihsan\SimpleAdminBundle\Controller\AbstractController
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * @param string $pageDescription
     * @return \Ihsan\SimpleAdminBundle\Controller\AbstractController
     */
    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;

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
     * @param string $formClass
     * @return \Ihsan\SimpleAdminBundle\Controller\AbstractController
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;

        return $this;
    }
}
