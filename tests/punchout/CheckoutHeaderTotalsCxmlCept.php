<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->switchToNetPrices();
$i->addToCartCanonPowerShot35();

$i->see('cart');
$prices = $i->getElement('.cart-summary .list.spacing-y .list__item');
$discount = $prices->first()->filter('.text-right')->text();
$discount = trim($discount, '-');
$discount = trim($discount);
$discount = trim($discount, '€');
$totalItemCount = $prices->count();
codecept_debug('Get discount from cart page: ' . $discount);
$grandTotal = $prices->eq($totalItemCount - 1)->filter('.float-right')->first()->text();
$grandTotal = trim($grandTotal, '€');
codecept_debug('Get grand total from cart page: ' . $grandTotal);
$tax = $prices->eq($totalItemCount - 2)->filter('.float-right')->first()->text();
$tax = trim($tax, '€');
codecept_debug('Get tax from cart page: ' . $tax);
$i->savePage('cart');

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
codecept_debug($data);
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<Total><Money currency="EUR">' . $grandTotal . '</Money></Total>');
$i->canSeeCxmlContains($data, '<Tax><Money currency="EUR">' . $tax . '</Money></Tax>');
$i->canSeeCxmlContains($data, '<Discount><Money currency="EUR">' . $discount . '</Money><Description>10% Discount for all orders above</Description></Discount>');
