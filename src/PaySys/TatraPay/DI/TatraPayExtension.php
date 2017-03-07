<?php

namespace PaySys\TatraPay\DI;

use Nette\DI\CompilerExtension;
use PaySys\TatraPay\Configuration;


class TatraPayExtension extends CompilerExtension
{
	const BASE_ROUTE = "TatraPay:TatraPay:process";

	/** @var [] */
	private $defaults = [
		"mid" => "",
		"rurl" => self::BASE_ROUTE,
		"key" => "",
	];

	public function loadConfiguration()
	{
		$this->validateConfig($this->defaults);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('config'))
			->setClass('PaySys\TatraPay\Configuration', [
				'mid' => $this->config['mid'],
				'rurl' => $this->config['rurl'],
				'key' => $this->config['key'],
			]);

		$builder->addDefinition($this->prefix('button'))
			->setImplement('PaySys\TatraPay\IButtonFactory')
			->setFactory('PaySys\PaySys\Button', [
				'config' => $this->prefix('@config'),
			]);

		$builder->addDefinition($this->prefix('request'))
			->setClass('PaySys\TatraPay\Security\Request');

		$builder->addDefinition($this->prefix('response'))
			->setClass('PaySys\TatraPay\Security\Response');
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		if ($this->config['rurl'] === self::BASE_ROUTE) {
			if ($builder->hasDefinition('routing.router')) {
				$netteRouter = $builder->getDefinition('routing.router');
				$netteRouter->addSetup('$service->prepend(new Nette\Application\Routers\Route(\'cardpay-process\', ?));', [self::BASE_ROUTE]);
			}

			if ($builder->hasDefinition('nette.presenterFactory')) {
				$builder->getDefinition('nette.presenterFactory')
					->addSetup('setMapping', [
						['TatraPay' => 'PaySys\TatraPay\Application\UI\*Presenter'],
					]);
			}
		}
	}
}
