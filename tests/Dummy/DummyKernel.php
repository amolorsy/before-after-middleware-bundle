<?php
declare(strict_types=1);

namespace BeforeAfterMiddlewareBundle\Tests\Dummy;

use BeforeAfterMiddlewareBundle\BeforeAfterMiddlewareBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Gyu Kang <gyu@gyukang.me>
 */
class DummyKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new BeforeAfterMiddlewareBundle()
        ];

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/{packages}/dummy_config.yml', 'glob');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import($this->getRootDir(), '/', 'annotation');
    }
}
