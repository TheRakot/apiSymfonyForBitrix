<?php

declare(strict_types=1);

namespace Classes\Facade;

/**
 * Class Globals
 *
 * @package Classes\Facade
 */
final class Globals
{
	/**
	 * Возвращает значение по ключу $name в $GLOBALS
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public static function get(string $name)
	{
		return $GLOBALS[$name];
	}
	
	/**
	 * Устанавливает $value по ключу $name в $GLOBALS
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return string $name
	 */
	public static function set(string $name, $value): string
	{
		$GLOBALS[$name] = $value;
		
		return $name;
	}
	
	/**
	 * @todo если не используется - удалить
	 * Возвращает имя фильтра $name
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function name(string $name): string
	{
		return self::get($name) ? $name : '';
	}
}
