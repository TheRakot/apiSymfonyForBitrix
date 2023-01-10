<?php

declare(strict_types=1);

namespace Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResponseFactory
 *
 * @package Api
 */
class ResponseFactory
{
	/**
	 * Генерирует ответ, если была ошибка
	 *
	 * @param Request $request
	 * @param int     $statusCode
	 * @param array   $messages
	 *
	 * @return JsonResponse
	 */
	public static function createErrorResponse(Request $request, int $statusCode, array $messages): JsonResponse
	{
		$errors = [];
		foreach ($messages as $message) {
			$errors[] = [
				'status' => (string)$statusCode,
				'code' => (string)$statusCode,
				'source' => [
					'pointer' => $request->getPathInfo()
				],
				'title' => $message,
				'detail' => $message,
			];
		}
		
		return new JsonResponse(['errors' => $errors], $statusCode);
	}
	
	/**
	 * Генерирует ответ для debug'а запросов
	 *
	 * @param $arDebug
	 *
	 * @return JsonResponse
	 */
	public static function createDebugResponse($arDebug): JsonResponse
	{
		return new JsonResponse(['debug' => $arDebug], 203);
	}
}
