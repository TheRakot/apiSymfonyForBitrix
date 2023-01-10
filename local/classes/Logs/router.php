<?php
/**
 * Интсансы логов
 */
namespace Classes\Logs;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LoggerInterface;
use RuntimeException;

//use \Monolog\Handler\TelegramBotHandler;

/**
 * Class router
 *
 * @package Classes\Logs
 *
 * debug — Подробная информация для отладки
 * info — Интересные события
 * notice — Существенные события, но не ошибки
 * warning — Исключительные случаи, но не ошибки
 * error — Ошибки исполнения, не требующие сиюминутного вмешательства
 * critical — Критические состояния (компонент системы недоступен, неожиданное исключение)
 * alert — Действие требует безотлагательного вмешательства
 * emergency — Система не работает
 */
class router
{
	public const DEFAULT_MONOLOG_LOG_DIR = '/home/bitrix/logs';
	
	private const LEVELS = [
		Logger::DEBUG => 'debug',
		Logger::INFO => 'info',
#		Logger::NOTICE => 'notice',
		Logger::WARNING => 'warning',
		Logger::ERROR => 'error',
		Logger::CRITICAL => 'critical',
#		Logger::ALERT => 'alert',
#		Logger::EMERGENCY => 'emergency',
#		Logger::API => 'api',
	];
########################################################################################################################
	/**
	 * Тестовый лог
	 * @var 
	 */
	protected static $test;

	/**
	 * @var
	 */
	protected static $cron;

########################################################################################################################

/**
 * @param string $log Существующий лог | null
 * @param string $name Название логера
 * @param bool $path Путь к логу | false
 *
 * @return Logger
 *@throws Exception
 */
	private static function getLog($log = null, $name, $path = false): Logger
	{
		//$log еще не является логером
		if (false === ($log instanceof LoggerInterface)) {
			
			$log = new Logger(ucfirst($name));

			//Место хранения лога
			if ($path) {
				$logPath = self::DEFAULT_MONOLOG_LOG_DIR . '/' . str_replace(['\\', ':', ';', ' '], '_', $path);
			}
			else {
				$logPath = self::DEFAULT_MONOLOG_LOG_DIR . '/' . $name;
			}
			
			# если папки нет — создаем
			if (false === realpath($logPath)) {
				self::createDir($logPath);
			}

			//Выбранный путь существует и является папкой ИЛИ успешно создали новую папку для лога
			if (file_exists($logPath)) {
				
				$dateFormat = 'Y-m-d H:i:s:u';
				$output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
				$output = "[%datetime%] %level_name%: %message% %context% %extra%\n";
				$formatter = new LineFormatter($output, $dateFormat);
				
				# регистрируем pushHandler согласно уровней логированя проекта
				foreach (self::LEVELS as $level => $levelName) {
					$logFile = sprintf('%s/%s.log', $logPath, $levelName);
					
					#храним логи 2 недели
					$stream = new RotatingFileHandler(
						$logFile,
						14,
						$level,
						true
					);
					$stream->setFormatter($formatter);
					$log->pushHandler($stream);
					
					if ($level === Logger::CRITICAL) {
						$log->pushHandler(
							new NativeMailerHandler(
								['nikoltun@gmail.com'],
								'CRITICAL event on ' . $_SERVER['HTTP_HOST'],
								'noreply@' . $_SERVER['HTTP_HOST'],
								Logger::CRITICAL,
								true
							)
						);
					}

					$log->pushHandler(
						new SyslogHandler(
							'learn',
							LOG_USER,
							$level,
							true
						)
					);

				}
			}
			else {
				throw new RuntimeException(sprintf('\'Cannot find / create logs directory "%s"\'', $logPath));
			}
		}
		return $log;
	}

    /**
     * @return mixed
     * @throws Exception
     */
	public static function getTest()
	{
		self::$test = self::getLog(self::$test, 'test', false);
		return self::$test;
	}

    /**
     * @return mixed
     * @throws Exception
     */
	public static function getCronAgents()
	{
		self::$cron = self::getLog(self::$cron, 'cron', false);
		return self::$cron;
	}
	
	/**
	 * @return LoggerInterface
	 * @throws Exception
	 */
	public static function getApiV1(): LoggerInterface
	{
		return self::getLog(null, 'api.v1', false);
	}

	/**
	 * Инициализация произвольного лога
	 *
	 * @param string $name Имя логера
	 * @param string | false $path Имя папки (длинное название) | false
	 *
	 * @return Logger
	 * @throws Exception
	 */
	public static function getLogByName($name, $path = false): Logger
	{
		return self::getLog(null, str_replace(['\\', ':', ';', ' '], '_', $name), $path);
	}
	
	/**
	 *  Создает папку $dir
	 * @param string $dir
	 */
	private static function createDir(string $dir): void
	{
		if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}
	}
}
