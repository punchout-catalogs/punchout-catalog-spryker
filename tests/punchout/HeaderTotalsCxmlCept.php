<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');


$i->wantTo('perform correct cxml setup request and see result');

$cxmlDynamicSetupRequestData = \Helper\Punchout::getCxmlDynamicSetupRequestData();
$i->sendPOST('/request?business-unit=16&store=de', $cxmlDynamicSetupRequestData);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="200" text="OK"/>');
$i->canSeeResponseContains('/access-token/');
$yvesUrl = $i->getAccessUrlFromXml();
$i->canSeeCorrectAccessUrl($yvesUrl);


$i->wantTo('Login by access url');

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
codecept_debug($data);
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<Total><Money currency="EUR">267.72</Money></Total>');
$i->canSeeCxmlContains($data, '<Tax><Money currency="EUR">42.75</Money></Tax>');
$i->canSeeCxmlContains($data, '<Discount><Money currency="EUR">29.75</Money><Description>10% Discount for all orders above</Description></Discount>');
