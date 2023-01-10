<?php

declare(strict_types=1);

namespace Classes\Utility;

use Psr\Log\LoggerInterface;

/**
 * Вспомогательный класс для отслеживания времени выполнения скриптов
 */
class Debug
{
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	
	/**
	 * @var int Делитель для перевода в секунды
	 */
	private $denominatorSecond = 1000000000;
	
	/**
	 * @var int Начало выполнения скрипта
	 */
	private $timeStart;
	
	/**
	 * @param LoggerInterface $logger
	 * @param bool            $toLog планируем писать в лог - добаивм разделитель строки
	 */
	public function __construct(LoggerInterface $logger, bool $toLog = false)
	{
		$this->logger = $logger;
		$this->timeStart = hrtime(true);
		
		if ($toLog) {
			$this->logger->debug('-------------');
		}
	}
	
	/**
	 * Пишем в Logger время в секундах
	 *
	 * @param string $message Сообщение
	 * @param array  $arData Контекст
	 */
	public function logS(string $message, array $arData = []): void
	{
		$time = hrtime(true) - $this->timeStart;
		$this->logger->debug(sprintf('%f с — %s', ($time/$this->denominatorSecond), $message), $arData);
	}
	
	/**
	 * Выводим на экран время в секундах
	 * @param string $message
	 */
	public function preS(string $message): void
	{
		$time = hrtime(true) - $this->timeStart;
		pre(sprintf('%f с — %s', ($time/$this->denominatorSecond), $message), true);
	}
}