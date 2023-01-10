<?php

declare(strict_types=1);

namespace Classes\Facade;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;
use CIBlockElement;
use CIBlockPropertyEnum;
use CIBlockSection;
use Classes\Logs\router;

/**
 * Class Iblock
 * Фасад для работы с классами модуля iblock
 *
 * @package Facade
 */
final class Iblock
{
	/**
	 * @var string
	 */
	private const LOG = 'facade.iblock';
	
	/**
	 * @var string код модуля
	 */
	private const NAME = 'iblock';
	
	/**
	 * Подключает модуль iblock
	 */
	private static function init(): void
	{
		Main::includeModule(self::NAME, router::getLogByName(self::LOG));
	}
	
	/**
	 *
	 * @return CIBlockSection
	 */
	public static function section(): CIBlockSection
	{
		self::init();
		return new CIBlockSection();
	}
	
	/**
	 *
	 * @return CIBlockElement
	 */
	public static function element(): CIBlockElement
	{
		self::init();
		return new CIBlockElement();
	}
	
	/**
	 * @return IblockTable
	 */
	public static function iblockTable(): IblockTable
	{
		self::init();
		return new IblockTable();
	}
	
	/**
	 * @return ElementTable
	 */
	public static function elementTable(): ElementTable
	{
		self::init();
		return new ElementTable();
	}
	
	/**
	 * @param array $params
	 *
	 * @return array
	 */
	public static function prepareFields(array $params): array
	{
		$fields = [
			'NAME' => '',
			'DETAIL_TEXT' => '',
			'PROPERTY_VALUES' => []
		];
		
		$properties = [];
		
		# подставляем переданные значения с принудительным сохранением self::FIELDS
		$fields = array_replace($fields, $params, ['DETAIL_TEXT', 'NAME', 'IBLOCK_ID']);
		$fields['PROPERTY_VALUES'] = array_replace($properties, $params['PROPERTY_VALUES']);
		
		return $fields;
	}
	
	/**
	 * @return CIBlockPropertyEnum
	 */
	public static function propertyEnum(): CIBlockPropertyEnum
	{
		self::init();
		return new CIBlockPropertyEnum();
	}
}
