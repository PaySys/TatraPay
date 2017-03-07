<?php

namespace PaySys\TatraPay;

use PaySys\PaySys\IPayment;


class Payment implements IPayment
{
	/** @var string */
	protected $amt;

	/** @var string */
	protected $vs;

	/** @var string */
	protected $curr = '978'; // EUR

	/** @var string */
	protected $timestamp;


	public function __construct(string $amt, string $vs)
	{
		$this->setAmount($amt);
		$this->setVariableSymbol($vs);
		$this->timestamp = gmdate('dmYHis');
	}

	public function setAmount($amt) : IPayment
	{
		if (!Validator::isAmount($amt))
			throw new \PaySys\PaySys\InvalidArgumentException(sprintf("Amount must have maximal 9 digits before dot and maximal 2 digits after. '%s' is invalid.", $amt));

		$this->amt = $amt;
		return $this;
	}

	public function getAmount() : string
	{
		return $this->amt;
	}

	public function setVariableSymbol(string $vs) : Payment
	{
		if (!Validator::isVariableSymbol($vs))
			throw new \PaySys\PaySys\InvalidArgumentException(sprintf("Variable symbol must have minimal 1 and maximal 10 digits. '%s' is invalid.", $vs));

		$this->vs = $vs;
		return $this;
	}

	public function getVariableSymbol() : string
	{
		return $this->vs;
	}

	public function getCurrency() : string
	{
		return $this->curr;
	}

	/**
	 * @internal
	 */
	public function setTimestamp(string $timestamp) : Payment
	{
		if (!Validator::isTimestamp($timestamp))
			throw new \PaySys\PaySys\InvalidArgumentException(sprintf("Timestamp '%s' is invalid.", $timestamp));

		$this->timestamp = $timestamp;
		return $this;
	}

	public function getTimestamp() : string
	{
		return $this->timestamp;
	}


	private static function normalizeCurrency(string $curr) : string
	{
		$curr2code = [
			'EUR' => '978',
		];

		return $curr2code[strtoupper($curr)] ?? $curr;
	}
}
