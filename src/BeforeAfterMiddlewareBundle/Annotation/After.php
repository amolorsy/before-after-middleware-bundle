<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Annotation;

/**
 * An annotation class for @After().
 *
 * @author Gyu Kang <gyu@gyukang.me>
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class After extends Middleware
{
}
