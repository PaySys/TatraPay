# PaySys\TatraPay

[![Build Status](https://travis-ci.org/PaySys/TatraPay.svg?branch=master)](https://travis-ci.org/PaySys/TatraPay)
[![Code Quality](https://scrutinizer-ci.com/g/PaySys/TatraPay/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PaySys/TatraPay/)
[![Code Coverage](https://scrutinizer-ci.com/g/PaySys/TatraPay/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PaySys/TatraPay/)
[![Packagist](https://img.shields.io/packagist/v/PaySys/tatrapay.svg)](https://packagist.org/packages/PaySys/TatraPay)
![MIT](https://img.shields.io/badge/license-MIT-blue.svg)

Library for implement TatraPay gateway ([v4.0 with HMAC & ECDSA](http://www.tatrabanka.sk/tatrapay/TatraPay_technicka_prirucka_HMAC.pdf)) from Tatra Banka in Nette framework.

## Requirements

Requires PHP 7.1 or later.

Use universal libraty [PaySys\PaySys](https://github.com/PaySys/PaySys).

## Installation

The best way to install Unique is use [Composer](http://getcomposer.org) package [`PaySys/TatraPay`](https://packagist.org/packages/PaySys/TatraPay).

```bash
$ composer require paysys/tatrapay
```

## Configuration

```yaml
extensions:
	TatraPay: PaySys\TatraPay\DI\TatraPayExtension

TatraPay:
	mid: '1234'
	key: '64-bit hexadecimal string'
```

## Events

### Object ```PaySys\PaySys\Button```

| Event               | Parameters                       | Description               |
| :------------------ | :------------------------------- | :------------------------ |
| $onBeforePayRequest | \PaySys\PaySys\IPayment $payment | Occurs before pay request |
| $onPayRequest       | \PaySys\PaySys\IPayment $payment | Occurs on pay request     |

### Service ```PaySys\TatraPay\Security\Response```

| Event       | Parameters                                     | Description                                  |
| :---------- | :--------------------------------------------- | :------------------------------------------- |
| $onResponse | array $parameters                              | Occurs on response from bank                 |
| $onSuccess  | array $parameters                              | Occurs on success payment response from bank |
| $onFail     | array $parameters                              | Occurs on fail payment response from bank    |
| $onError    | array $parameters, \PaySys\PaySys\Exception $e | Occurs on damaged response from bank         |

## Example

### Generating payment button

Set ```PaySys\TatraPay\Payment```.

Button need ```PaySys\PaySys\IConfiguration``` service. Use DI generated factory ```PaySys\TatraPay\IButtonFactory``` for getting configured ```PaySys\PaySys\Button``` component.

Now set ```$onPayRequest``` event on ```PaySys\PaySys\Button``` for redirect to TatraPay gateway. Signed redirect URL is genereated by service ```PaySys\TatraPay\Security\Request->getUrl(PaySys\TatraPay\Payment $payment)```.

```php
class OrderPresenter extends Presenter
{
	/** @var \PaySys\TatraPay\IButtonFactory @inject */
	public $tatraPayButtonFactory;

	/** @var \PaySys\TatraPay\Security\Request @inject */
	public $tatraPayRequest;

	protected function createComponentTatraPayButton()
	{
		$payment = new \PaySys\TatraPay\Payment("12.34", "00456", "John Doe");
		$button = $this->tatraPayButtonFactory->create($payment);
		$button->onPayRequest[] = function ($payment) {
			$this->redirectUrl($this->TatraPayRequest->getUrl($payment));
		};
		return $button;
	}
}

```

### Process payment response

#### Event-driven processing

Default is Bank response routed to included presenter ```TatraPay:TatraPay:process```. In this case are automatic called events on service ```PaySys\TatraPay\Security\Response```.

For processing payment by events use for example [Kdyby\Events](https://github.com/Kdyby/Events).

#### Own presenter

Too it's possible write own ```Nette\Application\UI\Presenter``` for hnadling payment. In this case are events called same as before example.

```php
class OrderPresenter extends Presenter
{
	/** @var Nette\Http\IRequest @inject */
	public $httpRequest;

	/** @var \PaySys\TatraPay\Security\Response @inject */
	public $bankResponse;

	public function actionProcessTatraPay()
	{
		try {
			$this->bankResponse->paid($this->httpRequest->getQuery());
			// store info about payment
			$this->flashMessage('Thanks for payment.', 'success');
		} catch (\PaySys\PaySys\Exception $e) {
			// log
			$this->flashMessage('Payment failed. Please try it later.', 'danger');
		}
		$this->redirect('finish');
	}
}
```

Now just add route to configuration:

```yaml
tatraPay:
	rurl: Order:processTatraPay
```

## Exceptions

```php
class \PaySys\PaySys\Exception extends \Exception {}
class \PaySys\PaySys\SignatureException extends \PaySys\PaySys\Exception {}
class \PaySys\PaySys\ServerException extends \PaySys\PaySys\Exception {}
class \PaySys\PaySys\InvalidArgumentException extends \PaySys\PaySys\Exception {}
class \PaySys\PaySys\ConfigurationException extends \PaySys\PaySys\Exception {}
```
