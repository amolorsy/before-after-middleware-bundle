<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Annotation;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
abstract class Middleware
{
    /** @var string */
    private $class;

    /** @var string */
    private $method;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setClass($data['class']);
        $this->setMethod($data['method']);
    }

    /**
     * @param string $class
     */
    private function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @param string $method
     */
    private function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function getMethod(): \ReflectionMethod
    {
        $reflectionClass = new \ReflectionClass($this->class);
        $method = $reflectionClass->getMethod($this->method);

        return $method;
    }
}
