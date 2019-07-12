<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');

$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_2,
    \Helper\Punchout::getCxmlDynamicSetupRequestData('user_2', 'user_2_pass')
);

$i->switchToNetPrices();
$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_CANON_POWERSHOT_35);
$i->see('cart');

$prices = $i->getElement('.cart-summary .list.spacing-y .list__item');
$discount = $prices->first()->filter('.text-right')->text();
$totalItemCount = $prices->count();
$discount = trim($discount, '-');
$discount = trim($discount);
$discount = trim($discount, '€');
$discount = '-' . $discount;
codecept_debug('Get discount from cart page: ' . $discount);
$tax = $prices->eq($totalItemCount - 2)->filter('.float-right')->first()->text();
$tax = trim($tax, '€');
codecept_debug('Get tax from cart page: ' . $tax);

$i->cartTransfer();

$data = $i->getUrlEncodedCxmlCartResponse();
$i->seeCxml($data);

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$lines = [
    [
        'name' => 'Estimated Tax',
        'description' => 'Estimated Tax',
        'buyerPartID' => 'tax',
        'currency' => 'EUR',
        'supplierID' => 'spryker_sup_3',
        'eom' => 'EA',
        'price' => $tax,
    ],
    [
        'name' => 'Estimated Discount',
        'description' => '10% Discount for all orders above',
        'buyerPartID' => 'discount',
        'currency' => 'EUR',
        'supplierID' => 'spryker_sup_3',
        'eom' => 'EA',
        'price' => $discount,
    ],
];

codecept_debug($data);

$tpl = '<ItemDetail><BuyerPartID>%s</BuyerPartID><ManufacturerPartID>%s</ManufacturerPartID><UnitPrice><Money currency="%s">%s</Money></UnitPrice><Description xml:lang="en-US"><ShortName>%s</ShortName>%s</Description><UnitOfMeasure>%s</UnitOfMeasure><SupplierID>%s</SupplierID></ItemDetail>';

foreach ($lines as $line) {
    $needle = sprintf($tpl, $line['buyerPartID'], $line['buyerPartID'], $line['currency'], $line['price'], $line['name'], $line['description'], $line['eom'], $line['supplierID']);
    codecept_debug('looking for cxml:');
    codecept_debug($needle);
    $i->canSeeCxmlContains($data, $needle);
}
