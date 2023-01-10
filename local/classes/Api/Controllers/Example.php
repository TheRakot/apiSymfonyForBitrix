<?php

declare(strict_types=1);

namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Example
{
	private const ENTITY_TYPE = 'example';
	/**
	 * @var AddressesService\Service
	 */
//	private $service;
	/**
	 * @var User\UsersRepository
	 */
	//private $usersRepository;
	/**
	 * @var User\AdminUser
	 */
//	private $adminUser;
	
	public function __construct(
		//AddressesService\Service $service,
	//	User\UsersRepository $usersRepository,
	//	User\AdminUser $adminUser
	) {
		/*
		$this->service = $service;
		$this->usersRepository = $usersRepository;
		$this->adminUser = $adminUser;
		*/
	}
	
	/**
	 * Тестовый запрос
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function exampleApi(Request $request): JsonResponse
	{
		return new JsonResponse([
			'data' => [
				[
					'type' => self::ENTITY_TYPE,
					'attributes' => [
						self::ENTITY_TYPE => 'test',
						'queryString' => $request->getQueryString(),
						'jsonRequest' => $request->attributes->get('jsonApi'),
						'params' => $request->request->all()
					]
				]
			]
		], JsonResponse::HTTP_OK);
		
		/*
		$login = $request->attributes->get('auth_user_login');
		$user = $this->usersRepository->getUserByLogin($login);
		$collection = $this->service->getAddressesForSendings($user, $this->adminUser);
		$senders = [];
		foreach ($collection as $sender) {
			$senders[] = $sender->getAddressString();
		}
		return new JsonResponse([
			'data' => [
				[
					'type' => self::ENTITY_TYPE,
					'attributes' => [
						self::ENTITY_TYPE => 'test',
						'queryString' => $request->getQueryString()
					]
				]
			]
		], JsonResponse::HTTP_OK);
		*/
	}
	
}
