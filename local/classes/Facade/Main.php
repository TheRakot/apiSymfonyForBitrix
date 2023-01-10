<?php

declare(strict_types=1);

namespace Classes\Facade;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CHTTP;
use CMain;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class Main
 *
 * @package Classes\Facade
 */
final class Main
{
	/**
	 * @var CMain
	 */
	private static CMain $app;

	/**
	 * Возвращает $APPLICATION
	 *
	 * @return CMain
	 */
	public static function app(): CMain
	{
		if (!(self::$app instanceof CMain)) {
			self::$app = Globals::get('APPLICATION');
		}

		return self::$app;
	}

	/**
	 * Подключает модуль $module
	 *
	 * @param string          $module
	 * @param LoggerInterface $logger
	 */
	public static function includeModule(string $module, LoggerInterface $logger): void
	{
		try {
			Loader::includeModule($module);
		} catch (LoaderException $e) {
			$logger->error("Не загружен модуль $module", ['notice' => $e->getMessage()]);
			trigger_error($e->getMessage());
		} catch (Exception $e) {
			trigger_error($e->getMessage());
		}
	}

	/**
	 * Устанавливает статус 404 и подключает 404.php
	 */
	public static function set404(): void
	{
		if (defined('ERROR_404')) {
			CHTTP::setStatus('404 Not Found');

			if (self::app()->RestartWorkarea()) {
				$docRoot = getenv('DOCUMENT_ROOT');

				/** @noinspection PhpIncludeInspection */
				include_once($docRoot . '/bitrix/modules/main/include/prolog_before.php');
				/** @noinspection PhpIncludeInspection */
				include_once($docRoot . '/404.php');
				exit;
			}
		}
	}
}