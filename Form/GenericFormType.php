<?php
namespace Ihsan\SimpleAdminBundle\Form;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ihsan\SimpleAdminBundle\Controller\CrudController;

class GenericFormType extends AbstractType
{
    const FORM_NAME = 'generic';

    /**
     * @var CrudController
     */
    protected $controller;

    public function __construct(CrudController $controller)
    {
        $this->controller = $controller;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->controller->entityProperties() as $key => $value) {
            $builder->add($value, null, array(
                'attr' => array(
                    'class' => 'btn btn-primary',
                )
            ));
        }

        $builder->add('save', 'submit', array(
            'label' => 'action.submit',
            'attr' => array(
                'class' => 'btn btn-primary',
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->controller->getEntityClass(),
            'translation_domain' => $this->container->getParameter('ihsan.simple_admin.translation_domain'),
            'intention'  => self::FORM_NAME,
        ));
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
}
