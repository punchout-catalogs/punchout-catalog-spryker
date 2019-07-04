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


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-ixus-180-10');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');


$i->wantTo('Change currency');

$i->submitForm('[action="/en/currency/switch"]', [
    'currency-iso-code' => 'CHF',
]);
$i->canSeeOptionIsSelected('[name="currency-iso-code"]', 'Swiss Franc');
$price = $i->getElement('.cart-summary .list .list__item .float-right')->last()->text();
$price = trim($price, 'CHF');
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
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[1]',
    'value' => 'CHF',
]);

