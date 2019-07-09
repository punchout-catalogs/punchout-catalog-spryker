<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');

$i->setupRequestCxml(
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

$i->wantTo('check two bundle products');

$xml = simplexml_load_string($data);

/** @var \SimpleXMLElement $node1 */
$b1 = current($xml->xpath('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="210_123"]/../..'));
$b1Attrs = (array)$b1->attributes();
$b1Attrs = $b1Attrs['@attributes'];

$i->assertEquals('1', $b1Attrs['quantity']);
$i->assertEquals('composite', $b1Attrs['itemType']);
$i->assertEquals('groupLevel', $b1Attrs['compositeItemType']);

/** @var \SimpleXMLElement $node2 */
$b2 = current($xml->xpath('/cXML/Message/PunchOutOrderMessage/ItemIn/ItemID/SupplierPartID[.="211_123"]/../..'));
$b2Attrs = (array)$b2->attributes();
$b2Attrs = $b2Attrs['@attributes'];

$i->assertEquals('1', $b2Attrs['quantity']);
$i->assertEquals('composite', $b2Attrs['itemType']);
$i->assertEquals('groupLevel', $b2Attrs['compositeItemType']);

$i->canSeeCxmlContains($data, 'parentLineNumber="'.$b1Attrs['lineNumber'].'" itemType="item"');
$i->canSeeCxmlContains($data, 'parentLineNumber="'.$b2Attrs['lineNumber'].'" itemType="item"');

$i->wantTo('check children products');

$b1Children = $xml->xpath('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="'.$b1Attrs['lineNumber'].'"]');
$b2Children = $xml->xpath('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="'.$b2Attrs['lineNumber'].'"]');

$i->assertNotEmpty($b1Children);
$i->assertNotEmpty($b2Children);

foreach ([$b1Children, $b2Children] as $idx => $children) {
    $i->wantTo('check children products of the product #' . $idx);
    
    foreach ($children as $item) {
        $itemAttrs = (array)$item->attributes();
        $itemAttrs = $itemAttrs['@attributes'];
        
        $i->assertNotEmpty($itemAttrs['quantity']);
        $i->assertEquals('item', $itemAttrs['itemType']);
    }
}
