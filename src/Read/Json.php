<?php declare(strict_types=1);

namespace h4kuna\Fio\Read;

use h4kuna\Fio\Exceptions;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;

/* readonly */ class Json implements Reader
{

	public function __construct(private ?TransactionFactory $transactionFactory = null)
	{
	}


	public function getExtension(): string
	{
		return self::JSON;
	}


	/**
	 * @throws Exceptions\ServiceUnavailable
	 */
	public function create(ResponseInterface $response): TransactionList
	{
		$content = $response->getBody()->getContents();

		if ($content === '') {
			$content = '{}';
		}

		try {
			$json = Utils\Json::decode($content);
		} catch (Utils\JsonException $e) {
			throw new Exceptions\ServiceUnavailable(sprintf('%s: %s', $e->getMessage(), $content), 0, $e);
		}
		assert($json instanceof \stdClass);

		return new TransactionList($json->accountStatement, $this->transactionFactory);
	}

}
