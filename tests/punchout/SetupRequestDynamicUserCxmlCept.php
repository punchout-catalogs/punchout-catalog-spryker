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

$i->wantTo('perform correct cxml setup request again with same user and see result');

$i->sendPOST('/request?business-unit=16&store=de', $cxmlDynamicSetupRequestData);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="200" text="OK"/>');
$i->canSeeResponseContains('/access-token/');
$yvesUrl2 = $i->getAccessUrlFromXml();
$i->canSeeCorrectAccessUrl($yvesUrl2);


$i->wantTo('Login by access url');

$i->amOnUrl($yvesUrl);
$i->seeCurrentUrlEquals('/en');


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-powershot-n-35');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');
$price = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right')->last()->text();
$price = trim($price, '€');
codecept_debug('Get product price from cart page: ' . $price);


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<ShortName>Canon PowerShot N</ShortName>');
$i->canSeeCxmlContains($data, '<Money currency="EUR">' . $price . '</Money>');
$i->canSeeCxmlContains($data, '<UnitOfMeasure>EA</UnitOfMeasure>');
