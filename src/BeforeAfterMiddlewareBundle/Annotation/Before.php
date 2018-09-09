<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Annotation;

/**
 * An annotation class for @Before().
 *
 * @author Gyu Kang <gyu@gyukang.me>
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Before extends Middleware
{
}
