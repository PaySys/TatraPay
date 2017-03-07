<?php

use PaySys\TatraPay\Configuration;
use PaySys\TatraPay\Payment;
use PaySys\TatraPay\Security\Request;
use PaySys\TatraPay\Security\Response;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/** By example on https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example */

const AMT = "1234.50";
const CURR = "978";
const MID = "9999";
const VS = "1111";
const RURL = "https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp";
const KEY = "31323334353637383930313233343536373839303132333435363738393031323132333435363738393031323334353637383930313233343536373839303132";
const TIMESTAMP = "01092014125505";

$config = new Configuration(MID, RURL, KEY);

$payment = new Payment(AMT, VS);
$payment->setTimestamp("01092014125505");

$request = new Request($config);
Assert::same(MID . AMT . CURR . VS . RURL . TIMESTAMP, $request->getSignString($payment));

Assert::same("93aad2abbfa17189e379199d1da82022138e8c5f48a1d8680d97bd17a8725997", $request->getSign($payment));

Assert::same("https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay?MID=" . MID . "&AMT=" . AMT . "&CURR=" . CURR . "&VS=" . VS . "&RURL=" . urlencode(RURL) . "&TIMESTAMP=" . TIMESTAMP . "&HMAC=" . $request->getSign($payment), (string) $request->getUrl($payment));

$response = new Response($config);

const RES = "OK";
const TID = "1";

$r = [
	'AMT' => AMT,
	'CURR' => CURR,
	'VS' => VS,
	'RES' => RES,
	'TID' => TID,
	'TIMESTAMP' => TIMESTAMP,
	'HMAC' => "89526f1a0ca5f743befcbbcb5420a39220b14d7123f95250b6ed3c8ae5a5b2e0",
	'ECDSA_KEY' => "1",
	'ECDSA' => "30440220300314d4fb46342460770fdf4834ff86cc92a0db4e598234bd6793e6a3949c8e02206f5f590b5838aadbb128d3182d77702a8844ab9aa57ff648abc3e0e632f31e94",
];


Assert::same(AMT . CURR . VS . RES . TID . TIMESTAMP, $response->getSignString($r));
Assert::same("89526f1a0ca5f743befcbbcb5420a39220b14d7123f95250b6ed3c8ae5a5b2e0", $response->getHmac($r));
Assert::same("1234.509781111OK10109201412550589526f1a0ca5f743befcbbcb5420a39220b14d7123f95250b6ed3c8ae5a5b2e0", $response->getSignString($r) . $response->getHmac($r));

$response->setPublicKey(1, "-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEozvFM1FJP4igUQ6kP8ofnY7ydIWksMDk1IKXyr/T
RDoX4sTMmmdiIrpmCZD4CLDtP0j2LfD7saSIc8kZUwfILg==
-----END PUBLIC KEY-----");

Assert::true($response->verified($r));
/*

Assert::same("-----BEGIN PUBLIC KEY-----\r\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEaq6djyzkpHdX7kt8DsSt6IuSoXjp\r\nWVlLfnZPoLaGKc/2BSfYQuFIO2hfgueQINJN3ZdujYXfUJ7Who+XkcJqHQ==\r\n-----END PUBLIC KEY-----", $response->getPublicKey(1));
