<?php namespace Digiom\ApiMegaplan\Utils;

use Exception;
use Digiom\ApiMegaplan\Responses\ErrorResponse;

/**
 * Class ClientException
 *
 * @package Digiom\ApiMegaplan\Utils
 */
class ClientException extends Exception
{
	/**
	 * @var int
	 */
	protected $statusCode;

	/**
	 * @var string
	 */
	protected $reasonPhrase;

	/**
	 * @var ErrorResponse
	 */
	protected $errorResponse;

	/**
	 * ClientException constructor.
	 *
	 * @param string $uri
	 * @param int $statusCode
	 * @param string $reasonPhrase
	 */
	public function __construct($uri, $statusCode, $reasonPhrase, $er = null)
	{
		parent::__construct($uri . ': ' . $statusCode . ' ' . $reasonPhrase, $statusCode);

		$this->statusCode = $statusCode;
		$this->reasonPhrase = $reasonPhrase;

		if($er instanceof ErrorResponse)
		{
			$this->errorResponse = $er;
		}
	}
}
