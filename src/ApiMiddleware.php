<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\RequestScopeStorage;
use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiMiddleware implements IMiddleware
{

	/** @var IDispatcher */
	protected $dispatcher;

	/** @var RequestScopeStorage */
	private $requestScopeStorage;

	public function __construct(IDispatcher $dispatcher, RequestScopeStorage $requestScopeStorage)
	{
		$this->dispatcher = $dispatcher;
		$this->requestScopeStorage = $requestScopeStorage;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		$this->requestScopeStorage->save('uri', $request->getUri());

		// Pass this API request/response objects to API dispatcher
		$response = $this->dispatcher->dispatch($request, $response);
		
		$this->requestScopeStorage->clear();

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

}
