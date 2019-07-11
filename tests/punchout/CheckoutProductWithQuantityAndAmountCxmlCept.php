<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$sku = '218_1234';

$i->switchToGrossPrices();

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

$bundles = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '65',
        'name' => 'Giftbox',
        'quantity' => $quantity,
    ],
];

foreach ($bundles as $bundle) {
    $i->wantTo('assert bundle product SKU: ' . $bundle['sku']);
    $idx = $bundle['idx'];
    
    /** @var \SimpleXMLElement $el */
    $xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="%s"]/../..', $bundle['sku']);
    $el = current($xml->xpath($xpath));
    $i->assertNotEmpty($el);
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $i->assertEquals($idx, $i->getAttributeValue($el, 'lineNumber'));
    $i->assertEquals($bundle['quantity'], $i->getAttributeValue($el, 'quantity'));
    $i->assertEquals('composite', $i->getAttributeValue($el, 'itemType'));
    $i->assertEquals('groupLevel', $i->getAttributeValue($el, 'compositeItemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    
    $i->assertEquals($bundle['name'],$i->getXpathValue($el, 'ItemDetail[1]/Description[1]/ShortName[1]'));
    $i->assertEquals($bundle['price'],$i->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]'));
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $childrenXpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="%s"]', $lineNumber);
    $children = $xml->xpath($childrenXpath);
    
    $i->wantTo('check number children products is 1 of the product SKU: ' . $bundle['sku']);
    $i->assertTrue(count($children) == 1);
    
    $i->wantTo('check children products of the product SKU: ' . $bundle['sku']);
    
    /** @var \SimpleXMLElement $childEl */
    foreach ($children as $childIdx => $childEl) {
        $i->wantTo('assert bundle product SKU: ' . $bundle['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertEquals('item', $i->getAttributeValue($childEl, 'itemType'));
        $i->assertEquals($amount, $i->getAttributeValue($childEl, 'quantity'));
        $i->assertNotEmptyCxmlElementBasicElements($childEl);
    }
}
