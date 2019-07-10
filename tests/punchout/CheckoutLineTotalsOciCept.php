<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);


$i->wantTo('perform correct oci setup request and see result');

$ociSetupRequestData = \Helper\Punchout::getOciSetupRequestData();
$i->sendPOST('/request?business-unit=16&store=de', $ociSetupRequestData);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

$yvesUrl = $i->getAccessUrlFromOci();
$i->canSeeCorrectAccessUrl($yvesUrl);


$i->wantTo('Login by access url');

$i->amOnUrl($yvesUrl);
$i->seeCurrentUrlEquals('/en');


$i->wantTo('Select net price mode');

$i->submitForm('[action="/en/price/mode-switch"]', [
    'price-mode' => 'NET_MODE',
]);
$i->canSeeOptionIsSelected('[name="price-mode"]', 'Net prices');


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-powershot-n-35');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');
$prices = $i->getElement('.cart-summary .list.spacing-y .list__item');
$discount = $prices->first()->filter('.text-right')->text();
$totalItemCount = $prices->count();
$discount = trim($discount, '-');
$discount = trim($discount);
$discount = trim($discount, '€');
codecept_debug('Get discount from cart page: ' . $discount);
$tax = $prices->eq($totalItemCount - 2)->filter('.float-right')->first()->text();
$tax = trim($tax, '€');
codecept_debug('Get tax from cart page: ' . $tax);


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');

$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[2]',
    'value' => 'Estimated Tax',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[2]',
    'value' => $tax,
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[2]',
    'value' => 'EUR',
]);

$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[3]',
    'value' => 'Estimated Discount',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[3]',
    'value' => '-' . $discount,
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[3]',
    'value' => 'EUR',
]);
