<?php

use PaySys\TatraPay\Payment;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$payment = new Payment("12.34", "00456");

Assert::same("12.34", $payment->getAmount());
Assert::same("00456", $payment->getVariableSymbol());
Assert::same("978", $payment->getCurrency());
Assert::match('#^\d{14}$#', $payment->getTimestamp());


Assert::exception(function() use ($payment) {
	$payment->setAmount(4);
}, "PaySys\\PaySys\\InvalidArgumentException", "Amount must have maximal 9 digits before dot and maximal 2 digits after. '4' is invalid.");
Assert::same("12.34", $payment->getAmount());

$payment->setAmount((string) 4);
Assert::same("4", $payment->getAmount());


Assert::exception(function() use ($payment) {
	$payment->setVariableSymbol(".-");
}, "PaySys\\PaySys\\InvalidArgumentException", "Variable symbol must have minimal 1 and maximal 10 digits. '.-' is invalid.");
Assert::same("00456", $payment->getVariableSymbol());

$payment->setVariableSymbol((string) 4);
Assert::same("4", $payment->getVariableSymbol());
