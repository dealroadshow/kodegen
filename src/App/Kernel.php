<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__, 2).'/config/services.yaml')) {
            $container->import('../../config/{services}.yaml');
            $container->import('../../config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__, 2).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    public function process(ContainerBuilder $container): void
    {
        $this->hideSystemCommands($container);
    }

    private function hideSystemCommands(ContainerBuilder $container): void
    {
        $ids = \array_keys($container->findTaggedServiceIds('console.command'));

        foreach ($ids as $id) {
            $commandDefinition = $container->getDefinition($id);
            if (!\str_starts_with($id, 'App')) {
                $commandDefinition->addMethodCall('setHidden', [true]);
            }
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
    }
}
