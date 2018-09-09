<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Tests;

use BeforeAfterMiddlewareBundle\Tests\Dummy\DummyMiddleware;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
class BeforeAfterMiddlewareBundleTest extends WebTestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param string $id
     * @param string $password
     * @param int $http_status_code
     */
    public function testBeforeAndAfterMiddleware(string $id, string $password, int $http_status_code)
    {
        $parameters = ['id' => $id, 'password' => $password];

        $client = self::createClient();
        $client->request('POST', '/', $parameters);

        $response = $client->getResponse();
        $this->assertSame($http_status_code, $response->getStatusCode());
        $this->assertSame(DummyMiddleware::DUMMY_TOKEN, $response->getContent());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [DummyMiddleware::DUMMY_ID, DummyMiddleware::DUMMY_PASSWORD, Response::HTTP_OK],
            [DummyMiddleware::DUMMY_ID, DummyMiddleware::DUMMY_WRONG_PASSWORD, Response::HTTP_BAD_REQUEST]
        ];
    }
}
