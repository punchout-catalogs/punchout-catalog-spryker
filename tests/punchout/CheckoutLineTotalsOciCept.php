<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->switchToNetPrices();
$i->addToCartCanonPowerShot35();
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

$i->cartTransfer();

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
