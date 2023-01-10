<?php

declare(strict_types=1);

use DI\Di;
use Symfony\Component\HttpFoundation;
use Symfony\Component\Routing;
use Symfony\Component\Routing\RouteCollection;

const NOT_CHECK_PERMISSIONS = true;
const PUBLIC_AJAX_MODE = true;
const BX_SECURITY_SESSION_VIRTUAL = true;
const STOP_STATISTICS = true;
const NO_KEEP_STATISTIC = 'Y';
const NO_AGENT_STATISTIC = 'Y';
const NO_AGENT_CHECK = true;
const DISABLE_EVENTS_CHECK = true;
const NEED_AUTH = false;
const BX_COMPRESSION_DISABLED = true;
const DisableEventsCheck = true;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = Di::container()->get("Psr\\Log\\LoggerInterfaceApiV1");

$request = HttpFoundation\Request::createFromGlobals();

if ($request->getContentType() == 'json') $request->attributes->set('jsonApi', json_decode($request->getContent()));
else $request->attributes->set('jsonApi', new stdClass());

					#оставим до введения авторизации
					#$authService = Di::container()->get(Api\Auth\Service::class);
					#$tokenRepository = Di::container()->get(Classes\Token\Repository::class);

$log->info('api.start', ['path' => $request->getPathInfo()]);

$route = new RouteCollection();
#Подключаем файлы (маршруты) из папки /api/v1/routes
foreach (glob(__DIR__ . '/routes/*.php') as $filename) {
	$newRoute = include $filename;
	$route->addCollection($newRoute);
}

$context = new Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Routing\Matcher\UrlMatcher($route, $context);
#pre($matcher, true);

#Входные параметры запроса
$log->info('api.jsonApi ', [$request->attributes->get('jsonApi')] );
$log->info('api.queryString ' . print_r($request->getQueryString(), true));
$log->info('api.parameters ' . print_r($request->request->all(), true));

try {
	$attributes = $matcher->match($request->getPathInfo());
	$log->info('api.attributes ', $attributes);
	
					#if ($attributes['_authorization']) {
					#	$login = (string)\Bitrix\Main\Engine\CurrentUser::get()->getLogin();
					#	$request = $authService->validateRequest($request, $login, new \DateTimeImmutable());
					#}
	
	$controller = Di::container()->get($attributes['_controller']);
	$method = $attributes['_method'];
	
	unset($attributes['_method'], $attributes['_controller'], $attributes['_route'], $attributes['_authorization']);
	
	foreach ($attributes as $key => $value) {
		$request->attributes->set($key, $value);
	}
	
	$log->info('api.run', ['_controller' => $controller, '_method' => $method]);
	
	$response = $controller->$method($request);
} catch (Routing\Exception\ResourceNotFoundException | Routing\Exception\MethodNotAllowedException $exception) {
	#Выведем список зарегистрированных end-point
	if ($context->getPathInfo() == '/api/v1/apiList/') {
		$apiList = [];
		foreach ($route->all() as $key => $item) {
			$apiList[] = [
				'name' => $key,
				'path' => $item->getPath(),
				'method' => $item->getMethods(),
			];
		}
		$response = new HttpFoundation\JsonResponse([
			'data' => [
				'type' => 'end-point-list',
				'attributes' => $apiList
			]
		], HttpFoundation\JsonResponse::HTTP_ACCEPTED);
	}
	else {
		$response = Api\ResponseFactory::createErrorResponse(
			$request,
			HttpFoundation\JsonResponse::HTTP_BAD_REQUEST,
			['Не найден ресурс']
		);
	}
	$log->notice('api.badRequest', ['method' => $context->getMethod(), 'path' => $context->getPathInfo(), 'query' => $context->getQueryString()]);
}
					/*catch (Classes\Token\Exceptions\TokenException $exception) {
						$response = Api\ResponseFactory::createErrorResponse(
							$request,
							HttpFoundation\JsonResponse::HTTP_UNAUTHORIZED,
							['Не пройдена аутентификация']
						);
						$log->notice('api.authorizationFail', ['exception' => $exception, 'path' => $request->getPathInfo()]);
					} */
	
 catch (Throwable $exception) {
	$response = Api\ResponseFactory::createErrorResponse(
		$request,
		HttpFoundation\JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
		['Не удалось обработать запрос', $exception->getMessage()]
	);
	 $log->critical(sprintf('api.uncaughtException %s', $exception->getMessage()),
		[
			'exception' => $exception,
			'line' => $exception->getLine(),
			'file' => $exception->getFile(),
			'trace' => $exception->getTrace()
		]
	 );
}
					header('Access-Control-Allow-Origin: *');
$response->send();
$log->info('api.end', ['path' => $request->getPathInfo()]);
