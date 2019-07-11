<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class PunchoutTester extends \Codeception\Actor
{
    use _generated\PunchoutTesterActions;
    
    public function setupRequestCxml($bu, $cxmlDynamicSetupRequestData)
    {
        $this->haveHttpHeader('content-type', 'text/xml');
        
        $this->sendPOST('/request?business-unit=' . $bu . '&store=de', $cxmlDynamicSetupRequestData);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $this->seeResponseIsXml();
        
        $this->canSeeXmlResponseIncludes('<Status code="200" text="OK"/>');
        $this->canSeeResponseContains('/access-token/');
        
        $yvesUrl = $this->getAccessUrlFromXml();
        $this->canSeeCorrectAccessUrl($yvesUrl);
        
        $this->wantTo('Login by access url');
        
        $this->amOnUrl($yvesUrl);
    
        $this->seeCurrentUrlEquals('/en');
        return $this;
    }
    
    public function setupRequestOci($bu, array $ociSetupRequestData)
    {
        $this->sendPOST('/request?business-unit=' . $bu . '&store=de', $ociSetupRequestData);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    
        $yvesUrl = $this->getAccessUrlFromOci();
        $this->canSeeCorrectAccessUrl($yvesUrl);
    
        $this->wantTo('Login by access url');
    
        $this->amOnUrl($yvesUrl);
        
        $this->seeCurrentUrlEquals('/en');
        return $this;
    }
    
    public function switchToGrossPrices()
    {
        $this->wantTo('Select gross mode');
        $this->submitForm('[action="/en/price/mode-switch"]', [
            'price-mode' => 'GROSS_MODE'
        ]);
        $this->canSeeOptionIsSelected('[name="price-mode"]', 'Gross prices');
        return $this;
    }
    
    public function switchToNetPrices()
    {
        $this->wantTo('Select net price mode');
        $this->submitForm('[action="/en/price/mode-switch"]', [
            'price-mode' => 'NET_MODE'
        ]);
        $this->canSeeOptionIsSelected('[name="price-mode"]', 'Net prices');
        return $this;
    }
    
    public function switchCurrencySwissFranc()
    {
        $this->wantTo('Change currency');
        $this->submitForm('[action="/en/currency/switch"]', [
            'currency-iso-code' => 'CHF',
        ]);
        $this->canSeeOptionIsSelected('[name="currency-iso-code"]', 'Swiss Franc');
        return $this;
    }
    
    public function addProductToCart($urlKey, $lang = 'en')
    {
        $url = "/$lang/$urlKey";
        
        $this->wantTo('Add product to cart: ' . $url);
        $this->amOnPage($url);
        $this->click('[id="add-to-cart-button"]');
        return $this;
    }
    
    public function addProductToCartWithOptions($urlKey, $sku, array $options, $lang = 'en')
    {
        $url = "/$lang/$urlKey";
        
        $this->wantTo('Add product to cart: ' . $url);
        $this->amOnPage($url);
        $this->submitForm('[action="/'.$lang.'/cart/add/'.$sku.'"]', $options);
        return $this;
    }
    
    public function cartTransfer()
    {
        $this->see('cart');
        $this->wantTo('Transfer cart');
        $this->stopFollowingRedirects();
        $this->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
        $this->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
        return $this;
    }
    
    public function getOciFormElements()
    {
        $data = [];
        $elements = $this->getElement('input');
        
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $name = $element->getAttribute('name');
            $value = $element->getAttribute('value');

            if (strpos($name, 'NEW_ITEM-LONGTEXT') === 0) {
                preg_match('~NEW_ITEM-(.*)_(\d+):132\[\]~', $name, $matches);
            } else {
                preg_match('~NEW_ITEM-(.*)\[(\d+)\]~', $name, $matches);
            }
            
            $data[$matches[2]][$matches[1]] = $value;
        }

        return $data;
    }
    
    public function toOciElementsTree(array $elements)
    {
        $data = [];
        foreach ($elements as $idx => $el) {
            $data[$el['PARENT_ID']][$idx] = $el;
        }
        return $data;
    }
    
    public function getAttributeValue(\SimpleXMLElement $el, string $attr)
    {
        $attrs = (array)$el->attributes();
        return isset($attrs['@attributes'][$attr]) ? $attrs['@attributes'][$attr] : null;
    }
    
    public function getXpathValue(\SimpleXMLElement $el, string $xpath)
    {
        $value = current($el->xpath($xpath));
        return ($value !== null && $value !== false) ? trim($value) : null;
    }
    
    public function assertNotEmptyOciElementBasicElements(array $el)
    {
        $this->assertNotEmpty($el['QUANTITY']);
        $this->assertNotEmpty($el['DESCRIPTION']);
        $this->assertTrue(null !== $el['PRICE']);
        $this->assertNotEmpty($el['UNIT']);
        $this->assertNotEmpty($el['EXT_PRODUCT_ID']);
        $this->assertNotEmpty($el['CURRENCY']);
        $this->assertNotEmpty($el['LONGTEXT']);
        $this->assertNotEmpty($el['VENDOR']);
        $this->assertNotEmpty($el['VENDORMAT']);
        return $this;
    }
    
    public function assertNotEmptyCxmlElementBasicElements(\SimpleXMLElement $el)
    {
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/Description[1]/ShortName[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/Description[1]'));
        $this->assertTrue(null !== $this->getXpathValue($el, 'ItemDetail[1]/UnitPrice[1]/Money[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/UnitOfMeasure[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/SupplierID[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/BuyerPartID[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemDetail[1]/ManufacturerPartID[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemID[1]/SupplierPartID[1]'));
        $this->assertNotEmpty($this->getXpathValue($el, 'ItemID[1]/SupplierPartAuxiliaryID[1]'));
        return $this;
    }
}
