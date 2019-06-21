<?php 
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');
$i->wantTo('perform wrong setup request format and see result');
$i->sendPOST('/request?business-unit=16', ['name' => 'test', 'email' => 'test@codeception.com']);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeResponseContains('<Status code="406" text="Not Acceptable">Invalid PunchOut Format</Status>');


$i->wantTo('perform correct setup request format and see result');

$i->sendPOST('/request?business-unit=16', \Helper\Punchout::getSetupRequestData());
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="200" text="OK"/>');
$i->canSeeResponseContains('/access-token/');

$yvesUrl = $i->getAccessUrl();

$i->amOnPage($yvesUrl);
$i->amOnUrl('http://www.de.suite-nonsplit.local/de/');
$i->amOnPage('http://www.de.suite-nonsplit.local/de/canon-powershot-n-35');

$i->click('[id="add-to-cart-button"]');

$i->see('cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$data = $i->getBase64CxmlCartResponse();
$i->isCxml(base64_decode($data));
$i->amOnUrl('http://www.de.suite-nonsplit.local/de/punchout-catalog/cart/transfer');
