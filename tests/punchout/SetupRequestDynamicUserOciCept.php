<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->wantTo('perform correct oci setup request again with same user and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_CANON_POWERSHOT_35);

$i->see('cart');

$price = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right')->last()->text();
$price = trim($price, '€');
codecept_debug('Get product price from cart page: ' . $price);

$i->cartTransfer();

$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[1]',
    'value' => 'Canon PowerShot N',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[1]',
    'value' => $price,
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[1]',
    'value' => 'EUR',
]);
