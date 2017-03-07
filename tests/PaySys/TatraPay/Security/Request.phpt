<?php

use PaySys\TatraPay\Configuration;
use PaySys\TatraPay\Payment;
use PaySys\TatraPay\Security\Request;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/** https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example */

$rurl = "https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp";

$config = new Configuration("9999", $rurl, "31323334353637383930313233343536373839303132333435363738393031323132333435363738393031323334353637383930313233343536373839303132");

$payment = new Payment("1234.50", "1111");
$payment->setTimestamp("01092014125505");

$request = new Request($config);
Assert::same("99991234.509781111https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/example.jsp01092014125505", $request->getSignString($payment));

Assert::same("93aad2abbfa17189e379199d1da82022138e8c5f48a1d8680d97bd17a8725997", $request->getSign($payment));

Assert::same("https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/tatrapay?MID=9999&AMT=1234.50&CURR=978&VS=1111&RURL=" . urlencode($rurl) . "&TIMESTAMP=01092014125505&HMAC=93aad2abbfa17189e379199d1da82022138e8c5f48a1d8680d97bd17a8725997", (string) $request->getUrl($payment));
