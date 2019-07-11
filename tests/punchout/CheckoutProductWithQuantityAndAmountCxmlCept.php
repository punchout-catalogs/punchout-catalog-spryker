<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->switchToGrossPrices();

$sku = '218_1234';

$i->addProductToCartWithOptions(
    \Helper\Punchout::PRODUCT_PU_SCREW_218_PACK_GIFTBOX,
    $sku,
    [
        'sales-unit-quantity' => 0.01,
        'quantity' => 2,
        'id-product-measurement-sales-unit' => 21,
        'amount-sales-unit' => [$sku => 1000],
        'amount' => [$sku => 400],
        'amount-id-product-measurement-sales-unit' => [$sku => 12]
    ]
);

$i->see('cart');

$quantity = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->first()->text();
codecept_debug('Get product quantity from cart page: ' . $quantity);

$amount = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->last()->text();
codecept_debug('Get product amount from cart page: ' . $amount);

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();

$i->seeCxml($data);

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$products = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '65',
        'name' => 'Giftbox',
        'quantity' => $quantity,
        'uom' => 'EA',
        'currency' => 'EUR',
    ],
];

foreach ($products as $product) {
    $i->wantTo('assert bundle product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    /** @var \SimpleXMLElement $el */
    $el = $i->getCxmlItemBySku($xml, $product['sku']);
    
    $i->assertNotEmpty($el);
    $i->assertCxmlProductItem($el, $product);
    $i->assertCxmlProductItemBundleComplexSpecific($el);
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $children = $i->getCxmlItemsByParentLineNumber($xml, $lineNumber);
    $i->wantTo('check number children products is 1 of the product SKU: ' . $product['sku']);
    $i->assertTrue(count($children) == 1);
    
    $i->wantTo('check children products of the product SKU: ' . $product['sku']);
    
    /** @var \SimpleXMLElement $childEl */
    foreach ($children as $childIdx => $childEl) {
        $i->wantTo('assert bundle product SKU: ' . $product['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertEquals('item', $i->getAttributeValue($childEl, 'itemType'));
        $i->assertEquals($amount, $i->getAttributeValue($childEl, 'quantity'));//
        $i->assertNotEmptyCxmlElementBasicElements($childEl);
    }
}
