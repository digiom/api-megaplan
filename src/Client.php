<?php namespace Digiom\ApiMegaplan;

use Exception;
use RuntimeException;
use Digiom\ApiMegaplan\Utils\HttpRequestExecutor;
use Digiom\Psr7wp\HttpClient;
use Digiom\ApiMegaplan\Utils\StringsTrait;

/**
 * Class Client
 *
 * @package Digiom\ApiMegaplan
 */
class Client
{
	use StringsTrait;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var string
	 */
	private $login;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $token = '';

	/**
	 * @var HttpClient
	 */
	private $httpClient;

	/**
	 * ApiClient constructor.
	 * Создаёт экземпляр коннектора API
	 *
	 * @param string $host Хост, на котором располагается API
	 * @param bool $forceHttps Форсировать запрос через HTTPS
	 * @param array $credentials Логин и пароль пользователя или токен пользователя
	 * @param HttpClient|null $http_client HTTP-клиент
	 *
	 * @throws Exception
	 */
	public function __construct(string $host, bool $forceHttps, array $credentials, HttpClient $http_client = null)
	{
		if(empty($host))
		{
			throw new RuntimeException('Hosts address cannot be empty or null!');
		}

		$host = trim($host);

		if($this->isInvalidCredentials($credentials))
		{
			throw new RuntimeException('Credential login, password or token must be set!');
		}

		while($this->endsWith($host, '/'))
		{
			$host = substr($host, 0, -1);
		}

		if($forceHttps)
		{
			if($this->startsWith($host, 'http://'))
			{
				$host = str_replace('http://', 'https://', $host);
			}
			elseif(!$this->startsWith($host, 'https://'))
			{
				$host = 'https://' . $host;
			}
		}
		elseif(!$this->startsWith($host, 'https://') && !$this->startsWith($host, 'http://'))
		{
			$host = 'http://' . $host;
		}

		$this->host = $host;

		if(is_null($http_client))
		{
			$http_client = new HttpClient();
		}

		$this->setHttpClient($http_client);

		$this->setCredentials($credentials);
	}

	/**
	 * Устанавливает данные доступа, которые используются для авторизации
	 * запросов к API
	 *
	 * @param array $credentials Массив данных для доступа
	 * [
	 *  login - логин в формате <code>[имя_пользователя]@[название_компании]</code>
	 *  password - пароль
	 *  token - Bearer токен авторизации
	 * ]
	 *
	 * @throws Exception
	 */
	public function setCredentials(array $credentials)
	{
		if(isset($credentials['token']))
		{
			$this->setToken($credentials['token']);
		}
		elseif(isset($credentials['login'], $credentials['password']))
		{
			$this->login = $credentials['login'];
			$this->password = $credentials['password'];
		}
		else
		{
			throw new RuntimeException('Credential login, password or token must be set!');
		}
	}

	/**
	 * Устанавливает Bearer токен авторизации запросов к API
	 *
	 * @param string $token Bearer токен авторизации
	 */
	public function setToken(string $token)
	{
		$this->token = $token;
	}

	/**
	 * Устанавливает пользовательский HTTP-клиент, с помощью которого будут выполняться запросы.
	 *
	 * @param HttpClient $client
	 */
	public function setHttpClient(HttpClient $client)
	{
		$this->httpClient = $client;
	}

	/**
	 * @return HttpClient
	 */
	public function getHttpClient(): HttpClient
	{
		return $this->httpClient;
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getLogin(): string
	{
		return $this->login;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getToken(): string
	{
		return $this->token;
	}

	/**
	 * @param array $credentials
	 *
	 * @return bool
	 */
	private function isInvalidCredentials(array $credentials): bool // todo: test connecting with param
	{
		return (!isset($credentials['login']) && !isset($credentials['password'])) && !isset($credentials['token']);
	}

	/**
	 * Произвольный запрос к API по пути
	 *
	 * @param string $path
	 *
	 * @return HttpRequestExecutor
	 */
	public function api(string $path): HttpRequestExecutor
	{
		return HttpRequestExecutor::path($this, $path);
	}
}
