<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');


$i->wantTo('perform correct cxml setup request and see result');

$i->sendPOST('/request?business-unit=16&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData());
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="200" text="OK"/>');
$i->canSeeResponseContains('/access-token/');


$i->wantTo('Login by access url');

$yvesUrl = $i->getAccessUrlFromXml();
$i->amOnUrl($yvesUrl);
$i->seeCurrentUrlEquals('/en');


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-powershot-n-35');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<ShortName>Canon PowerShot N</ShortName>');
$i->canSeeCxmlContains($data, '<Money currency="EUR">267.72</Money>');
