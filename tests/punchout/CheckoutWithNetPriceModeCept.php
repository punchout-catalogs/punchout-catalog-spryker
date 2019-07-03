<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);


$i->wantTo('perform correct oci setup request and see result');

$i->sendPOST('/request?business-unit=16&store=de', \Helper\Punchout::getOciSetupRequestData());
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);


$i->wantTo('Login by access url');

$yvesUrl = $i->getAccessUrlFromOci();
$i->amOnUrl($yvesUrl);
$i->seeCurrentUrlEquals('/en');


$i->wantTo('Set Euro currency');

$i->submitForm('[action="/en/currency/switch"]', [
    'currency-iso-code' => 'EUR',
]);
$i->canSeeOptionIsSelected('[name="currency-iso-code"]', 'Euro');


$i->wantTo('Select gross mode');

$i->submitForm('[action="/en/price/mode-switch"]', [
    'price-mode' => 'NET_MODE',
]);
$i->canSeeOptionIsSelected('[name="price-mode"]', 'Net prices');


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-ixus-180-10');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');
$price = $i->getElement('.cart-summary .list .list__item .float-right')->last()->text();
$price = trim($price, '€');
codecept_debug('Get product price from cart page: ' . $price);


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[1]',
    'value' => 'Canon IXUS 180',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[1]',
    'value' => $price,
]);

