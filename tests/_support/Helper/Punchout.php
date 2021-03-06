<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\InnerBrowser;
use Codeception\Util\Xml;
use Codeception\Util\XmlStructure;

class Punchout extends \Codeception\Module
{
    const BUSINESS_UNIT_USER_1 = 16;
    const BUSINESS_UNIT_USER_2 = 19;
    
    const ALL_TOTAL_SKUS = ['tax', 'discount', 'expense'];

    const PRODUCT_BUNDLE_SONY_210 = 'sony-bundle-210';
    const PRODUCT_BUNDLE_HP_211 = 'hp-bundle-211';
    
    const PRODUCT_SIMPLE_CANON_IXUS_180 = 'canon-ixus-180-10';
    const PRODUCT_SIMPLE_CANON_POWERSHOT_35 = 'canon-powershot-n-35';
    
    const PRODUCT_SIMPLE_SAMSUNG_GALAXY_OPT_128GB = 'samsung-galaxy-s6-edge-51?attribute[storage_capacity]=128+GB';
    const PRODUCT_SIMPLE_SAMSUNG_GALAXY_OPT_64GB = 'samsung-galaxy-s6-edge-51?attribute[storage_capacity]=64+GB';
    
    const PRODUCT_PU_ASUS_HDMI_217_PACK_RING_500 = 'Asus-HDMI-HDMI-215?attribute[packaging_unit]=Ring+(500m)';
    const PRODUCT_PU_SCREW_218_PACK_GIFTBOX = 'Screw-218?attribute[packaging_unit]=Giftbox';
    const PRODUCT_PU_SCREW_218_PACK_MIXED = 'Screw-218?attribute[packaging_unit]=Mixed';
    
    const PRODUCT_SONY_FDR_AX40 = 'sony-fdr-ax40-193';

    /**
     * @param $selector
     * @return \Symfony\Component\DomCrawler\Crawler
     * @throws \Codeception\Exception\ModuleException
     */
    public function getElement($selector)
    {
        return $this->getModule('PhpBrowser')->_findElements($selector);
    }

    public static function getOciSetupRequestData($username = 'user_1', $password = 'user_1_pass', $email = null)
    {
        if (empty($email)) {
            $email = 'oci' . uniqid() . '@punchoutcatalogs.net';
        }
        return [
            "HOOK_URL" => "http://localhost:8899/simulator/cart/receive.php",
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "first_name" => "Oci",
            "last_name" => "Tester",
        ];
    }

    public static function getCxmlDynamicSetupRequestData($identity = 'user_1', $sharedSecret = 'user_1_pass', $email = null)
    {
        if (empty($email)) {
            $email = 'cxml' . uniqid() . '@punchoutcatalogs.net';
        }
        
        return <<<XML_DATA
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.007/cXML.dtd">
<cXML version="1.1.007" xml:lang="en-US" payloadID="486698.1561035135.895@localhost:8899" timestamp="2019-06-20UTC12:52:15+00:00">
    <Header>
        <From>
            <Credential domain="http://localhost:8899">
                <Identity>$identity</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="http://localhost:8899">
                <Identity>$identity</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="http://localhost:8899">
                <Identity>$identity</Identity>
                <SharedSecret>$sharedSecret</SharedSecret>
            </Credential>
            <UserAgent>Punchout Cloud Simulator 0.1.0</UserAgent>
        </Sender>
    </Header>
    <Request>
        <PunchOutSetupRequest operation="create">
            <BuyerCookie>poc5d0b817f59d4c</BuyerCookie>

            <Extrinsic name="FirstName">cXML</Extrinsic>
            <Extrinsic name="LastName">Tester</Extrinsic>
            <Extrinsic name="UserEmail">$email</Extrinsic>

            <Contact>
                <Name xml:lang="en-US">cXML Tester</Name>
                <Email>$email</Email>
            </Contact>

            <BrowserFormPost>
                <URL>http://localhost:8899/simulator/cart/receive.php</URL>
            </BrowserFormPost>

            <ShipTo>
                <Address>
                    <PostalAddress>
                        <DeliverTo>cXML Tester</DeliverTo>
                        <Street>Great Ocean ave, bd. 145, ap. 44</Street>
                        <City>Eureka</City>
                        <State>CA</State>
                        <PostalCode>95501</PostalCode>
                        <Country isoCountryCode="US">United States</Country>
                    </PostalAddress>
                </Address>
            </ShipTo>

        </PunchOutSetupRequest>
    </Request>
</cXML>
XML_DATA;

    }

    /**
     * For debug purposes
     *
     * @param $name
     * @throws \Codeception\Exception\ModuleException
     */
    public function savePage($name)
    {
        $dir = codecept_output_dir();
        $this->getModule('PhpBrowser')->_savePageSource($dir . $name . '.html');
    }

    /**
     * Assert is $xml has cXML format
     *
     * @param $xml
     */
    public function seeCxml($xml)
    {
        $this->assertContains('<cXML', $xml, 'is cXML');
    }

    /**
     * Retrieve cxml content from web page
     *
     * @return bool|string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getBase64CxmlCartResponse()
    {
        /** @var InnerBrowser $module */
        $module = $this->getModule('PhpBrowser');
        $html = $module->_getResponseContent();
        $structure = new XmlStructure(Xml::toXml($html));
        $value = $structure->matchElement('#punchoutCartForm [name="cxml-base64"]')->getAttribute('value');
        return base64_decode($value);
    }
    
    /**
     * Retrieve cxml content from web page
     *
     * @return bool|string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUrlEncodedCxmlCartResponse()
    {
        /** @var InnerBrowser $module */
        $module = $this->getModule('PhpBrowser');
        $html = $module->_getResponseContent();
        $structure = new XmlStructure(Xml::toXml($html));
        $value = $structure->matchElement('#punchoutCartForm [name="cxml-urlencoded"]')->getAttribute('value');
        return $value;
    }
    
    /**
     * Retrieve html content from web page
     *
     * @return bool|string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getHtmlResponse()
    {
        /** @var InnerBrowser $module */
        $module = $this->getModule('PhpBrowser');
        return $module->_getResponseContent();
    }
    
    /**
     * Retrieve cxml access url from web page
     *
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getAccessUrlFromXml()
    {
        $response = $this->getModule('REST')->response;
        $xml = Xml::toXml($response);
        $structure = new XmlStructure($xml);
        $url = $structure->matchElement('//cXML/Response/PunchOutSetupResponse/StartPage/URL')->textContent;
        return $url;
    }

    /**
     * Retrieve oci access url from web page
     *
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getAccessUrlFromOci()
    {
        $response = $this->getModule('REST')->response;
        $xml = Xml::toXml($response);
        $structure = new XmlStructure($xml);
        $url = $structure->matchElement('//body/script')->textContent;
        return trim(str_replace(["window.location.href = '", "';"], ['', ''], $url));
    }

    /**
     * @param $xml
     * @param $text
     */
    public function canSeeCxmlContains($xml, $text)
    {
        $this->assertContains($text, $xml);
    }
    
    /**
     * @param $xml
     * @param $text
     */
    public function canNotSeeCxmlContains($xml, $text)
    {
        $this->assertNotContains($text, $xml);
    }
    
    /**
     * @param $url
     */
    public function canSeeCorrectAccessUrl($url)
    {
        $this->assertRegExp('/[a-zA-Z0-9][a-zA-Z0-9-.]+\/en\/access-token\/[a-zA-Z0-9-_\.]+/m', $url);
    }
}
