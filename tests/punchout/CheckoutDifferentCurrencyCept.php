<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->addToCartCanonIxus180();
$i->switchToGrossPrices();
$i->switchCurrencySwissFranc();

$price = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right')->last()->text();
$price = trim($price, 'CHF');
codecept_debug('Get product price from cart page: ' . $price);

$i->cartTransfer();

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
