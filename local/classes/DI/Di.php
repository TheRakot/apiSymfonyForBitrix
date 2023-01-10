<?php

declare(strict_types=1);

namespace DI;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Di
{
	/**
	 * @var ContainerInterface
	 */
	private static $container;
	
	public static function load(string $pathToFile): void
	{
		$pattern = "#(?'subdir'.*\/)(?'file'.*)#i";
		preg_match_all($pattern, $pathToFile, $matches);
		$subDir = $matches['subdir'][0];
		$fileName = $matches['file'][0];
		self::$container = new ContainerBuilder();
		$loader = new YamlFileLoader(self::$container, new FileLocator($subDir));
		$loader->load($fileName);
		self::container()->compile();
	}
	
	public static function container(): ContainerInterface
	{
		return self::$container;
	}
}
