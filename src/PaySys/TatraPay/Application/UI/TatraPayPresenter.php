<?php

namespace PaySys\TatraPay\Application\UI;

use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use PaySys\TatraPay\Security\Response;


class TatraPayPresenter extends Presenter
{

	/** @var IRequest @inject */
	public $httpRequest;

	/** @var Response @inject */
	public $bankResponse;

	public function actionProcess()
	{
		try {
			$this->bankResponse->paid($this->httpRequest->getQuery());
		} catch (\PaySys\PaySys\Exception $e) {
		}
		$this->terminate();
	}
}
