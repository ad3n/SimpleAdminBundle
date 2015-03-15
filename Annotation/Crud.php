<?php
namespace Ihsan\SimpleCrudBundle\Annotation;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Crud
{
    public $entityClass;
}
