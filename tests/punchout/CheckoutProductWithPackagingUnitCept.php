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

$i->amOnPage('/en/Asus-HDMI-HDMI-215?attribute%5Bpackaging_unit%5D=Ring+%28500m%29');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');
$quantity = $i->getElement('[data-qa="quantity-input"]')->last()->attr('value');
codecept_debug('Get product quantity from cart page: ' . $quantity);

$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<UnitOfMeasure>EA</UnitOfMeasure>');
$i->canSeeCxmlContains($data, "<ItemIn lineNumber=\"1\" quantity=\"$quantity\">");
