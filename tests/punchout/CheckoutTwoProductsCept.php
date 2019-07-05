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


$i->wantTo('Select gross mode');

$i->submitForm('[action="/en/price/mode-switch"]',[
    'price-mode'=>'GROSS_MODE',
]);
$i->canSeeOptionIsSelected('[name="price-mode"]', 'Gross prices');


$i->wantTo('Add first product to cart');

$i->amOnPage('/en/samsung-galaxy-s6-edge-51?attribute%5Bstorage_capacity%5D=128+GB');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');

$i->wantTo('Add second product to cart');

$i->amOnPage('/en/samsung-galaxy-s6-edge-51?attribute%5Bstorage_capacity%5D=64+GB');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');
$prices = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right');
$price1 = $prices->first()->text();
$price2 = $prices->last()->text();
$price1 = trim($price1, '€');
$price2 = trim($price2, '€');
codecept_debug('Get product #1 price from cart page: ' . $price1);
codecept_debug('Get product #2 price from cart page: ' . $price2);


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[1]',
    'value' => 'Samsung Galaxy S6 edge',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[2]',
    'value' => 'Samsung Galaxy S6 edge',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[1]',
    'value' => $price1,
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[2]',
    'value' => $price2,
]);

