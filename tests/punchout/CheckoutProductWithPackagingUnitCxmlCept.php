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
$i->addProductToCart(\Helper\Punchout::PRODUCT_PU_ASUS_HDMI_217_PACK_RING_500);
$i->see('cart');

$quantity = $i->getElement('[data-qa="quantity-input"]')->last()->attr('value');
codecept_debug('Get product quantity from cart page: ' . $quantity);

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$products = [
    [
        'idx' => '1',
        'sku' => '215_124',
        'price' => '12.5',
        'name' => 'ASUS HDMI-HDMI Red',
        'currency' => 'EUR',
        'quantity' => $quantity,
        'uom' => 'EA',
    ],
];

foreach ($products as $product) {
    $i->wantTo('check product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    /** @var \SimpleXMLElement $el */
    $xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="%s"]/../..', $product['sku']);
    $el = current($xml->xpath($xpath));
    $i->assertNotEmpty($el);
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $i->assertEquals($idx, $i->getAttributeValue($el, 'lineNumber'));
    $i->assertEquals($product['quantity'], $i->getAttributeValue($el, 'quantity'));
    $i->assertEmpty($i->getAttributeValue($el, 'itemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'compositeItemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    
    $i->assertEquals($product['name'],$i->getXpathValue($el, 'ItemDetail[1]/Description[1]/ShortName[1]'));
    $i->assertEquals($product['price'],$i->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]'));
    $i->assertEquals($product['currency'],$i->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]/@currency'));
    $i->assertEquals($product['uom'],$i->getXpathValue($el, 'ItemDetail[1]/UnitOfMeasure[1]'));
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canNotSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $i->wantTo('check there is not any child product of the product SKU: ' . $product['sku']);
    $childrenXpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="%s"]', $lineNumber);
    $children = $xml->xpath($childrenXpath);
    $i->assertEmpty($children);
}

$i->wantTo('check all products exists in cXML Order Message and all are simple items');

$xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn');
$elements = $xml->xpath($xpath);
$i->assertNotEmpty($elements);

$skus = array_column($products, 'sku');
$skus = array_merge($skus, \Helper\Punchout::ALL_TOTAL_SKUS);

/** @var \SimpleXMLElement $el */
foreach ($elements as $elIdx => $el) {
    $i->wantTo('check is product common values ' . $elIdx);
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $sku = $i->getXpathValue($el, 'ItemID[1]/SupplierPartID[1]');
    
    $i->wantTo('check if SKU is expected: ' . $sku);
    $i->assertTrue(in_array($sku, $skus));
}
