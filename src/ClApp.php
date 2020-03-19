<?php

declare(strict_types=1);

namespace ClApp;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;



class ClApp extends \Symfony\Component\Console\Application
{

    const DEFAULT_APPLICATION_NAME = 'symfony-console-di-application';
    const DEFAULT_APPLICATION_VERSION = '@package_version@';

    const COMMAND_SERVICE_TAG = 'console.command';

    /**
     * @var ContainerBuilder
     */
    protected $container;



    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = self::DEFAULT_APPLICATION_NAME, $version = self::DEFAULT_APPLICATION_VERSION)
    {
        parent::__construct($name, $version);

        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('config.yml');

        $this->container->compile();

        $this->addCustomCommands();
    }



    private function addCustomCommands()
    {
        foreach ($this->container->findTaggedServiceIds(self::COMMAND_SERVICE_TAG) as $commandId => $command) {
            /** @var Command $command */
            $command = $this->container->get($commandId);
            $this->add($command);
        };
    }
}
