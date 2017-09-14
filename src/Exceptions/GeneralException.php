<?php
namespace oizdar\ExceptionHandler\Exceptions;

class GeneralException extends \Exception
{
	/** @var int */
	protected $httpCode = 400;

	public function __construct($message = "", $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	public function getHttpCode() : int
	{
		return (int)$this->httpCode;
	}

	public function getCodeString() : string
	{
		$class = explode('\\', get_called_class());
		$class = array_pop($class);
		$error = str_replace('Exception', '_Error', $class);
		return strtoupper($error);
	}
}
