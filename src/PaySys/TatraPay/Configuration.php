<?php

namespace PaySys\TatraPay;

use Nette;
use Nette\Application\LinkGenerator;
use Nette\Utils\Strings;
use PaySys\PaySys\IConfiguration;


/**
 * @method string getMid()
 * @method string getRurl()
 * @method string getKey()
 * @method string getIpc()
 * @method string getLang()
 * @method string getMode()
 * @method string getRem()
 */
final class Configuration implements IConfiguration
{

	/** @var string */
	private $mid;

	/** @var string */
	private $rurl;

	/** @var string */
	private $lang = 'sk';

	/** @var string */
	private $key;

	/** @var string */
	private $rem = '';

	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var string */
	private $buttonTemplate;


	public function __construct(string $mid, $rurl, string $key, LinkGenerator $linkGenerator = NULL)
	{
		$this->linkGenerator = $linkGenerator;
		$this->setMid($mid);
		$this->setRurl($rurl);
		$this->setKey($key);
		$this->setButtonTemplate(__DIR__ . '/template/button.latte');
	}

	public function setMid(string $mid) : Configuration
	{
		if (!Validator::isMid($mid))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("Parameter MID must have 3 or 4 characters. '%s' is invalid.", $mid));

		$this->mid = $mid;
		return $this;
	}

	public function setRurl($originalRurl) : Configuration
	{
		$supportedTypes = ['string', 'array'];
		if (!in_array(gettype($originalRurl), $supportedTypes))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("RURL type of '%s' is invalid. Must be %s.", gettype($originalRurl), implode(' or ', $supportedTypes)));

		$rurl = $originalRurl;
		if ($this->linkGenerator instanceof LinkGenerator) {
			try {
				if (is_string($originalRurl)) {
					$rurl = $this->linkGenerator->link($originalRurl);
				} elseif (is_array($originalRurl)) {
					$rurl = $this->linkGenerator->link($originalRurl['dest'], @$originalRurl['params']);
				}
			} catch (Nette\Application\UI\InvalidLinkException $e) {}
		}

		if (!Validator::isRurl($rurl))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("RURL '%s' is invalid. Must be valid URL by RFC 1738.", $originalRurl));

		$this->rurl = $rurl;
		return $this;
	}

	public function setKey(string $originalKey) : Configuration
	{
		$key = $this->getNormalizedKey($originalKey);
		if (!Validator::isKey($key))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("Key '%s' is invalid. Must have 64 byte standard string or 128 byte in hexadecimal format.", $originalKey));

		$this->key = $key;
		return $this;
	}

	public function setLang(string $originalLang) : Configuration
	{
		$lang = strtolower($originalLang);
		if (!Validator::isLang($lang))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("Lang '%s' is not supported.", $originalLang));

		$this->lang = $lang;
		return $this;
	}

	public function setRem(string $rem) : Configuration
	{
		if (!Nette\Utils\Validators::isEmail($rem))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("'%s' is not valid e-mail.", $rem));

		$this->rem = $rem;
		return $this;
	}

	public function setButtonTemplate(string $path) : Configuration
	{
		if (!file_exists($path))
			throw new \PaySys\PaySys\ConfigurationException(sprintf("Template file '%s' not exists.", $path));

		$this->buttonTemplate = $path;
		return $this;
	}

	public function getButtonTemplate() : string
	{
		return $this->buttonTemplate;
	}

	public function __call($method, $arguments) : string
	{
		return $this->{strtolower(substr($method, 3))};
	}

	private function getNormalizedKey(string $originalKey) : string
	{
		if (strlen($originalKey) === 128 OR strlen($originalKey) === 191) {
			if (strlen($originalKey) === 191) {
				$key = str_replace(':', '', $originalKey);
			} else {
				$key = $originalKey;
			}

			if (strlen($key) === 128)
				return pack("H*", $key);
		}
		return $originalKey;
	}
}
