<?php

namespace Hyperflex\Config;


use Hyperflex\Utils\Composer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

class ConfigFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $configPath = BASE_PATH . '/config/';
        $config = $this->readConfig($configPath . 'config.php');
        $serverConfig = $this->readConfig($configPath . 'server.php');
        $autoloadConfig = $this->readPaths([BASE_PATH . '/config/autoload']);
        $merged = array_replace_recursive(ProviderConfig::load(), $serverConfig, $config, ...$autoloadConfig);
        return new Config($merged);
    }

    private function readConfig(string $configPath): array
    {
        $config = [];
        if (file_exists($configPath) && is_readable($configPath)) {
            $config = require $configPath;
        }
        return is_array($config) ? $config : [];
    }

    private function readPaths(array $paths)
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[] = [
                $file->getBasename('.php') => require $file->getRealPath(),
            ];
        }
        return $configs;
    }

}