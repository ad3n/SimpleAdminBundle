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

class GenericFormType extends AbstractType
{
    const FORM_NAME = 'generic';

    public function getName()
    {
        return self::FORM_NAME;
    }
}
