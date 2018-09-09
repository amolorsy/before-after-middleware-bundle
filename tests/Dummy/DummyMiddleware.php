<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Tests\Dummy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
class DummyMiddleware
{
    const DUMMY_ID = 'dummy';
    const DUMMY_PASSWORD = '123456';
    const DUMMY_WRONG_PASSWORD = '123457';
    const DUMMY_TOKEN = 'abcdefghijklmnopqrstuwxyz!@#$%^&*()1234567890';

    /**
     * A dummy before middleware which validates 'id' and 'password' parameters.
     * If parameters aren't valid, a Response with Bad Request(400) HTTP status code will be returned.
     *
     * @param Request $request
     * @return null|Response
     */
    public static function validateIdAndPassword(Request $request): ?Response
    {
        $id = $request->get('id');
        $password = $request->get('password');

        if ($id !== self::DUMMY_ID || $password !== self::DUMMY_PASSWORD) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

    /**
     * A dummy after middleware which set a token on response.
     *
     * @param Request $request
     * @param Response $response
     */
    public static function setToken(Request $request, Response $response): void
    {
        $response->setContent(self::DUMMY_TOKEN);
    }
}
