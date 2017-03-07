<?php

namespace PaySys\TatraPay;

use Nette\Utils\Validators;


class Validator
{

	public static function isAmount($s) : bool
	{
		return (is_string($s) && preg_match('/^\d{1,9}(\.\d{1,2})?$/', $s));
	}

	public static function isVariableSymbol($s) : bool
	{
		return (is_string($s) && preg_match('/^\d{1,10}$/', $s));
	}

	public static function isCurrency($s) : bool
	{
		return (is_string($s) && $s === '978');
	}

	public static function isLang($s) : bool
	{
		return (is_string($s) && in_array($s, [
			'sk',
			'en',
		]));
	}

	public static function isMid($s) : bool
	{
		return (is_string($s) && preg_match('/^\d{3,4}$/', $s));
	}

	public static function isKey($s) : bool
	{
		$hex = '[0-9a-f]{2}';
		return (is_string($s) && preg_match('/^(.{64}|.{128}|(' . $hex . ':){63}' . $hex . ')$/', $s));
	}

	public static function isRurl($s) : bool
	{
		return (is_string($s) && Validators::isUri($s));
	}

	public static function isTimestamp($s) : bool
	{
		return (
			is_string($s) &&
			preg_match('/^\d{14}$/', $s) &&
			\DateTime::createFromFormat('dmYHis', $s, new \DateTimeZone('UTC')) instanceof \DateTime &&
			$s === \DateTime::createFromFormat('dmYHis', $s, new \DateTimeZone('UTC'))->format('dmYHis')
		);
	}

}
