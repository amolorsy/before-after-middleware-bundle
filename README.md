# Installation

```
composer require gyu-kang/before-after-middleware-bundle
```

And add `BeforeAfterMiddlewareBundle` to your `config/bundles.php`.
```
// config/bundles.php

<?php

return [
    ...
    BeforeAfterMiddlewareBundle\BeforeAfterMiddlewareBundle::class => ['all' => true]
];
```

# Usage

Methods annotated with `@Before` are executed before a request.  
And methods annotated with `@After` are executed after a request.

Below is an example.
```
// App\Controller\HomeController

<?php

namespace App\Controller; 

use BeforeAfterMiddlewareBundle\Annotation\After;
use BeforeAfterMiddlewareBundle\Annotation\Before;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     * @Before(class="App\Library\Middleware", method="beforeMiddlewareFunc"))
     * @After(class="App\Library\Middleware", method="afterMiddlewareFunc")
     *
     * @param Request $request
     * @return Response
     */
    public function hello(Request $request): Response
    {
        return new Response();
    }
}
```

```
// App\Library\Middleware

<?php

namespace App\Library; 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Middleware
{
    /**
     * @param Request $request
     * @return null|Response
     */
    public static function beforeMiddlewareFunc(Request $request): ?Response
    {
        $id = $request->get('id');
        $password = $request->get('password');

        if ($id !== 'test' || $password !== '123456') {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public static function afterMiddlewareFunction(Request $request, Response $response): void
    {
        $token = 'abcdefghijklmnopqrstuwxyz!@#$%^&*()1234567890';
        $response->setContent($token);
    }
}
```

Middleware methods must be `static`!

And when middleware methods must be `void` or nullable return types.
