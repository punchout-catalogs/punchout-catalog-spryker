<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');

$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_2,
    \Helper\Punchout::getCxmlDynamicSetupRequestData('user_2', 'user_2_pass')
);

$i->switchToGrossPrices();
$i->addToCartBundleProductSony210();
$i->addToCartBundleProductHp211();
$i->cartTransfer();

$data = $i->getUrlEncodedCxmlCartResponse();
$i->seeCxml($data);

$i->canSeeCxmlContains($data, '<SupplierPartID>210_123</SupplierPartID>');
$i->canSeeCxmlContains($data, '<SupplierPartID>211_123</SupplierPartID>');

$i->wantTo('check two bundle products exists in cXML Order Message');

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$bundles = [
    [
        'idx' => '1',
        'sku' => '210_123',
        'price' => '1000',
        'name' => 'Sony Bundle',
    ],
    [
        'idx' => '2',
        'sku' => '211_123',
        'price' => '705',
        'name' => 'HP Bundle',
    ],
];

foreach ($bundles as $bundle) {
    $i->wantTo('check bundle product SKU: ' . $bundle['sku']);
    $idx = $bundle['idx'];
    
    /** @var \SimpleXMLElement $el */
    $xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="%s"]/../..', $bundle['sku']);
    $el = current($xml->xpath($xpath));
    $i->assertNotEmpty($el);
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $i->assertEquals($idx, $i->getAttributeValue($el, 'lineNumber'));
    $i->assertEquals('1', $i->getAttributeValue($el, 'quantity'));
    $i->assertEmpty($i->getAttributeValue($el, 'itemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'compositeItemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    
    $i->assertEquals($bundle['name'],$i->getXpathValue($el, 'ItemDetail[1]/Description[1]/ShortName[1]'));
    $i->assertEquals($bundle['price'],$i->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]'));
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canNotSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $i->wantTo('check there is not any child product of the product SKU: ' . $bundle['sku']);
    $childrenXpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="%s"]', $lineNumber);
    $children = $xml->xpath($childrenXpath);
    $i->assertEmpty($children);
}

$i->wantTo('check two all products exists in cXML Order Message and all are simple items');

$xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn');
$elements = $xml->xpath($xpath);
$i->assertNotEmpty($elements);

$skus = array_column($bundles, 'sku');
$skus[] = 'tax';
$skus[] = 'discount';
$skus[] = 'expense';

/** @var \SimpleXMLElement $el */
foreach ($elements as $elIdx => $el) {
    $i->wantTo('check is product common values ' . $elIdx);
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $sku = $i->getXpathValue($el, 'ItemID[1]/SupplierPartID[1]');
    
    $i->wantTo('check if SKU is expected: ' . $sku);
    $i->assertTrue(in_array($sku, $skus));
}
