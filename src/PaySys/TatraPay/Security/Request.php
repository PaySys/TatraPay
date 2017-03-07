<?php

namespace PaySys\TatraPay\Security;

use Nette\Http\Url;
use PaySys\TatraPay\Configuration;
use PaySys\TatraPay\Payment;


final class Request
{
	const SERVER = "https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay";

	/** @var Configuration */
	protected $config;


	public function __construct(Configuration $config)
	{
		$this->config = $config;
	}

	public function getUrl(Payment $payment) : Url
	{
		$s = $this->getSign($payment);
		$url = new Url(self::SERVER);
		$url->appendQuery('MID=' . $this->config->getMid())
			->appendQuery('AMT=' . $payment->getAmount())
			->appendQuery('CURR=' . $payment->getCurrency())
			->appendQuery('VS=' . $payment->getVariableSymbol())
			->appendQuery('RURL=' . $this->config->getRurl());
		if (!empty($this->config->getRem()))
			$url->appendQuery('REM=' . $this->config->getRem());
		$url->appendQuery('TIMESTAMP=' . $payment->getTimestamp())
			->appendQuery('HMAC=' . $this->getSign($payment));
		return $url;
	}

	public function getSign(Payment $payment) : string
	{
		return hash_hmac("sha256", $this->getSignString($payment), $this->config->getKey());
	}

	public function getSignString(Payment $payment) : string
	{
		return $this->config->getMid()
			. $payment->getAmount()
			. $payment->getCurrency()
			. $payment->getVariableSymbol()
			. $this->config->getRurl()
			. $this->config->getRem()
			. $payment->getTimestamp();
	}

}
