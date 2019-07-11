<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->wantTo('Add product to cart');

$i->amOnPage('/en/Screw-218?attribute%5Bpackaging_unit%5D=Giftbox');

$i->submitForm('[action="/en/cart/add/218_1234"]', [
    'sales-unit-quantity' => 0.01,
    'quantity' => 2,
    'id-product-measurement-sales-unit' => 21,
    'amount-sales-unit' => ['218_1234' => 1000],
    'amount' => ['218_1234' => 400],
    'amount-id-product-measurement-sales-unit' => ['218_1234' => 12]
]);

$i->see('cart');

$quantity = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->first()->text();
codecept_debug('Get product quantity from cart page: ' . $quantity);

$amount = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->last()->text();
codecept_debug('Get product amount from cart page: ' . $amount);

$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');

$data = $i->getBase64CxmlCartResponse();

$i->seeCxml($data);

$i->canSeeCxmlContains($data, "<ItemIn lineNumber=\"1\" itemType=\"composite\" compositeItemType=\"groupLevel\" quantity=\"$quantity\">");
$i->canSeeCxmlContains($data, "<ItemIn lineNumber=\"2\" parentLineNumber=\"1\" itemType=\"item\" quantity=\"$amount\">");
