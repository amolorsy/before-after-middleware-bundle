<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Subscriber;

use BeforeAfterMiddlewareBundle\Annotation\After;
use BeforeAfterMiddlewareBundle\Annotation\Before;
use BeforeAfterMiddlewareBundle\Annotation\Middleware;
use Doctrine\Common\Annotations\CachedReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
class BeforeAfterMiddlewareSubscriber implements EventSubscriberInterface
{
    /** @var CachedReader */
    private $annotationReader;

    /** @var Before[] */
    private $beforeMiddlewares;

    /** @var After[] */
    private $afterMiddlewares;

    /**
     * @param CachedReader $annotationReader
     */
    public function __construct(CachedReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        if (!is_array($event->getController())) {
            return;
        }
        [$controller, $methodName] = $event->getController();

        $this->beforeMiddlewares = $this->getBeforeMiddlewares($controller, $methodName);
        $this->afterMiddlewares = $this->getAfterMiddlewares($controller, $methodName);

        if (empty($this->beforeMiddlewares) || empty($this->afterMiddlewares)) {
            return;
        }

        $request = $event->getRequest();
        foreach ($this->beforeMiddlewares as $middleware) {
            self::executeBeforeMiddleware($middleware, $request, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (empty($this->afterMiddlewares)) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        foreach ($this->afterMiddlewares as $middleware) {
            self::executeAfterMiddleware($middleware, $request, $response, $event);
        }
    }

    /**
     * @param $controller
     * @param string $methodName
     * @return Before[]
     * @throws \ReflectionException
     */
    public function getBeforeMiddlewares($controller, $methodName): array
    {
        return self::getMiddlewaresByAnnotation($controller, $methodName, Before::class);
    }

    /**
     * @param $controller
     * @param string $methodName
     * @return After[]
     * @throws \ReflectionException
     */
    public function getAfterMiddlewares($controller, $methodName): array
    {
        return self::getMiddlewaresByAnnotation($controller, $methodName, After::class);
    }

    /**
     * @param $controller
     * @param string $methodName
     * @param string $annotationClass
     * @return Middleware[]
     * @throws \ReflectionException
     */
    private function getMiddlewaresByAnnotation($controller, string $methodName, string $annotationClass): array
    {
        $reflectionObject = new \ReflectionClass($controller);
        $reflectionMethod = $reflectionObject->getMethod($methodName);
        $methodAnnotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);

        return array_filter(
            $methodAnnotations,
            function ($annotation) use ($annotationClass) {
                return ($annotation instanceof $annotationClass);
            }
        );
    }

    /**
     * @param Before $beforeMiddleware
     * @param Request $request
     * @param FilterControllerEvent $event
     * @throws \ReflectionException
     */
    private function executeBeforeMiddleware(
        Before $beforeMiddleware,
        Request $request,
        FilterControllerEvent $event
    ): void {
        $method = $beforeMiddleware->getMethod();
        $result = $method->invokeArgs(null, [$request]);
        if (!is_null($result)) {
            $event->setController(function () use ($result) {
                return $result;
            });
        }
    }

    /**
     * @param After $afterMiddleware
     * @param Request $request
     * @param Response $response
     * @param FilterResponseEvent $event
     * @throws \ReflectionException
     */
    public function executeAfterMiddleware(
        After $afterMiddleware,
        Request $request,
        Response $response,
        FilterResponseEvent $event
    ): void {
        $method = $afterMiddleware->getMethod();
        $method->hasReturnType();
        $result = $method->invokeArgs(null, [$request, $response]);
        if (!is_null($result) && $result instanceof Response) {
            $event->setResponse($result);
        }
    }
}
