<?php
namespace oizdar\ExceptionHandler;

use oizdar\CommunicationStandard\Error;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use oizdar\ExceptionHandler\Exceptions\GeneralException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ExceptionHandlerProvider implements ServiceProviderInterface
{
	/** @var array */
	protected $handlers = [];

	public function __construct(array $handlers = [])
	{
		$handlers[] = ['class' => GeneralException::class, 'callback' => [$this, 'handleCommonException']];
		$handlers[] = ['class' => \Exception::class, 'callback' => [$this, 'handleException']];

		$this->handlers = $handlers;

	}

	protected function handleException(Application $app)
	{
		$app->error(function(\Exception $e, Request $request, $httpCode) use ($app) {
			$data = [];
			if($app['debug'] === true) {
				$data['trace'] = $e->getTraceAsString();
			}
			return new Error($e->getMessage(), $data);
		});
	}

	protected function handleCommonException(Application $app)
	{
		$app->error(function(GeneralException $e, Request $request, $httpCode) use ($app) {
			$data = [];
			if($app['debug'] === true) {
				$data['trace'] = $e->getTraceAsString();
			}
			return new Error($e->getMessage(), $data, $e->getCodeString(), $e->getHttpCode());
		});
	}

	public function register(Container $app)
	{
		foreach($this->handlers as $handler) {
			if(isset($handler['class'])
				&& !empty($handler['class'])
				&& isset($handler['callback'])
				&& !empty($handler['callback'])
			) {
				call_user_func($handler['callback'], $app);
			}
		}
	}
}
