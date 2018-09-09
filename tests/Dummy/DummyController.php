<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Tests\Dummy;

use BeforeAfterMiddlewareBundle\Annotation\After;
use BeforeAfterMiddlewareBundle\Annotation\Before;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
class DummyController extends Controller
{
    /**
     * @Route("/", methods={"POST"})
     * @Before(class="BeforeAfterMiddlewareBundle\Tests\Dummy\DummyMiddleware", method="validateIdAndPassword"))
     * @After(class="BeforeAfterMiddlewareBundle\Tests\Dummy\DummyMiddleware", method="setToken")
     *
     * @param Request $request
     * @return Response
     */
    public function dummyLogin(Request $request): Response
    {
        return new Response();
    }
}
