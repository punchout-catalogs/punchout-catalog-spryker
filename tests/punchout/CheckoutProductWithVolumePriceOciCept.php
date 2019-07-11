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

$sku = '193_32124735';

$quantity = 5;

$i->addProductToCartWithOptions(
    \Helper\Punchout::PRODUCT_SONY_FDR_AX40,
    $sku,
    [
        'quantity' => $quantity,
    ]
);

$i->see('cart');

$i->cartTransfer();

$elements = $i->getOciFormElements();
$tree = $i->toOciElementsTree($elements);

$i->assertNotEmpty($elements);

$products = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '1.65',
        'name' => 'Sony FDR-AX40',
        'currency' => 'EUR',
        'quantity' => $quantity,
        'uom' => 'EA',
    ],
];

foreach ($products as $product) {
    $i->wantTo('check product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertOciProductItemBundleSingleSpecific($elements[$idx]);
    $i->assertOciProductItem($elements[$idx], $product);

    $i->wantTo('check there is not any child product of the product SKU: ' . $product['sku']);
    $i->assertTrue(!isset($tree[$idx]));
}

$i->wantTo('check all products exists in OCI Order Message and all are simple items');
$i->assertNotEmpty($elements);

$skus = array_column($products, 'sku');
$skus = array_merge($skus, \Helper\Punchout::ALL_TOTAL_SKUS);

/** @var array $el */
foreach ($elements as $elIdx => $el) {
    $i->wantTo('check is product common values ' . $elIdx);
    $i->assertNotEmptyOciElementBasicElements($el);

    $i->assertEmpty($el['PARENT_ID']);
    $i->assertEmpty($el['ITEM_TYPE']);

    $sku = $el['VENDORMAT'];

    $i->wantTo('check if SKU is expected: ' . $sku);
    $i->assertTrue(in_array($sku, $skus));
}
