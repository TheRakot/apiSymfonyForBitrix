<?php

declare(strict_types=1);

namespace Facade;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\DB\Result;
use CModule;

/**
 * Class HighLoadBlock
 *
 * @package Facade
 */
class HighLoadBlock
{
	private $highLoadBlockId;
	private $entityDataClass;
	
	/**
	 * HighLoadBlock constructor.
	 *
	 * @param int $highLoadBlockId
	 */
	public function __construct(int $highLoadBlockId)
	{
		try {
			CModule::IncludeModule('highloadblock');
			$this->highLoadBlockId = $highLoadBlockId;
			$highLoadBlock = HighloadBlockTable::getById($this->highLoadBlockId)->fetch();
			$entity = HighloadBlockTable::compileEntity($highLoadBlock);
			$this->entityDataClass = $entity->getDataClass();
		}
		catch (\Throwable $exception) {
			#todo KOLTNI подключить Sentry
			\Sentry\captureException($exception);
		}
	}
	
	/**
	 * @param array $select       массив фильтров в части SELECT запроса, возможны алиасы (необходимо зарегистрировать в секции $runTimeAlias)
	 *                            <code>'alias'=&gt;'field'</code><code>'COURSE_NAME' => 'ELEMENT.NAME'</code>
	 * @param array $filter       массив фильтров в части WHERE запроса в виде: <code>'(condition)field'=&gt;'value'</code>
	 * @param array $order        массив полей в части ORDER BY запроса в виде: <code>'field'=&gt;'asc|desc'</code>
	 * @param array $runTime      массив полей сущности, создающихся динамически
	 * @param array $runTimeAlias Переопределяет runtime! <code>'UF_COURSE_ID' => 'ELEMENT'</code>
	 * @param array $groupBy      массив полей в части GROUP BY запроса
	 *
	 * @return Result
	 */
	public function getList(array $select, array $filter, array $order, array $runTime = [], array $runTimeAlias = [], array $groupBy = []): Result
	{
		if (!empty($runTimeAlias)) {
			$runTime = [];
			foreach ($runTimeAlias as $key=>$value) {
				$runTime[$value] = [
					'data_type' => '\Bitrix\Iblock\ElementTable',
					'reference' => [
						'=this.' . $key => 'ref.ID'
					],
					'join_type' => 'inner'
				];
			}
		}
		
		try {
			return $this->entityDataClass::getList(
				[
					'select' => $select, //выбираем поля
					'filter' => $filter,
					'order' => $order, // сортировка по полю
					'group' => $groupBy,
					'runtime' => $runTime
				]
			);
		}
		catch (\Bitrix\Main\ArgumentException $exception) {
			\Sentry\captureException($exception);
		}
	}
	
	/**
	 * @param int   $id
	 * @param array $data
	 *
	 * @return \Bitrix\Main\Entity\UpdateResult
	 */
	public function update(int $id, array $data)
	{
		try {
			return $this->entityDataClass::update($id, $data);
		}
		catch (\Bitrix\Main\ArgumentException $exception) {
			\Sentry\captureException($exception);
		}
	}
	
	/**
	 * @return int
	 */
	public function getHighLoadBlockId(): int
	{
		return $this->highLoadBlockId;
	}

	public function getEntityDataClass()
	{
		return $this->entityDataClass;
	}
}

