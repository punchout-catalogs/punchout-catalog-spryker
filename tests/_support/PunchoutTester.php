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
    
    /**
     * Define custom actions here
     */
    
    public function setupRequestCxml($cxmlDynamicSetupRequestData)
    {
        $this->haveHttpHeader('content-type', 'text/xml');
        
        $this->sendPOST('/request?business-unit=16&store=de', $cxmlDynamicSetupRequestData);
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
    
    public function switchToGrossPrices()
    {
        $this->wantTo('Select gross mode');
        
        $this->submitForm('[action="/en/price/mode-switch"]', ['price-mode' => 'GROSS_MODE']);
        
        $this->canSeeOptionIsSelected('[name="price-mode"]', 'Gross prices');
    }
    
    public function addToCartBundleProductSony210()
    {
        $this->wantTo('Add sony-bundle-210 product to cart');
        $this->amOnPage('/en/sony-bundle-210');
        $this->click('[id="add-to-cart-button"]');
    }
    
    public function addToCartBundleProductHp211()
    {
        $this->wantTo('Add hp-bundle-211 product to cart');
        $this->amOnPage('/en/hp-bundle-211');
        $this->click('[id="add-to-cart-button"]');
        return $this;
    }
    
    public function cartTransfer()
    {
        $this->see('cart');
    
        $this->wantTo('Transfer cart');
    
        $this->stopFollowingRedirects();
        $this->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
        $this->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');
    }
}
