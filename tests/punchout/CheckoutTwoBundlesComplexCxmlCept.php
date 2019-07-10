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

$i->addToCartBundleProductSony210();
$i->addToCartBundleProductHp211();

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
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
    $i->wantTo('assert bundle product SKU: ' . $bundle['sku']);
    $idx = $bundle['idx'];
    
    /** @var \SimpleXMLElement $el */
    $xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="%s"]/../..', $bundle['sku']);
    $el = current($xml->xpath($xpath));
    $i->assertNotEmpty($el);
    $i->assertNotEmptyCxmlElementBasicElements($el);
    
    $i->assertEquals($idx, $i->getAttributeValue($el, 'lineNumber'));
    $i->assertEquals('1', $i->getAttributeValue($el, 'quantity'));
    $i->assertEquals('composite', $i->getAttributeValue($el, 'itemType'));
    $i->assertEquals('groupLevel', $i->getAttributeValue($el, 'compositeItemType'));
    $i->assertEmpty($i->getAttributeValue($el, 'parentLineNumber'));
    
    $i->assertEquals($bundle['name'],$i->getXpathValue($el, 'ItemDetail[1]/Description[1]/ShortName[1]'));
    $i->assertEquals($bundle['price'],$i->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]'));
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $childrenXpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="%s"]', $lineNumber);
    $children = $xml->xpath($childrenXpath);
    $i->assertNotEmpty($children);
    
    $i->wantTo('check children products of the product SKU: ' . $bundle['sku']);
    
    /** @var \SimpleXMLElement $childEl */
    foreach ($children as $childIdx => $childEl) {
        $i->wantTo('assert bundle product SKU: ' . $bundle['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertNotEmpty($i->getAttributeValue($childEl, 'quantity'));
        $i->assertEquals('item', $i->getAttributeValue($childEl, 'itemType'));
        $i->assertNotEmptyCxmlElementBasicElements($childEl);
    }
}
