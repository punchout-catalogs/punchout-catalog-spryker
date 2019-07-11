<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->switchToGrossPrices();

$i->wantTo('Add first product to cart');
$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_SAMSUNG_GALAXY_OPT_128GB);

$i->wantTo('Add second product to cart');
$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_SAMSUNG_GALAXY_OPT_64GB);

$i->see('cart');

$prices = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right');
$price1 = $prices->first()->text();
$price2 = $prices->last()->text();
$price1 = trim($price1, '€');
$price2 = trim($price2, '€');
codecept_debug('Get product #1 price from cart page: ' . $price1);
codecept_debug('Get product #2 price from cart page: ' . $price2);

$i->cartTransfer();

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
