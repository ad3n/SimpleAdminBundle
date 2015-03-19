<?php
namespace Ihsan\SimpleAdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    protected $pageTitle = 'IhsanSimpleAdminBundle';

    protected $pageDescription = 'Provide Admin Generator with KISS Principle';

    protected $showFields = array();

    protected $entityClass;

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
     * @return \Ihsan\SimpleAdminBundle\Controller\CrudController
     */
    public function setShowFields(array $fields)
    {
        $this->showFields = $fields;

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
}
