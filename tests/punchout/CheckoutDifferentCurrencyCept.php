<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_CANON_IXUS_180);
$i->switchToGrossPrices();
$i->switchCurrencySwissFranc();
$i->see('cart');

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
