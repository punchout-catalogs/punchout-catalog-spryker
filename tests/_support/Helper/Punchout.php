<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\InnerBrowser;
use Codeception\Util\Xml;
use Codeception\Util\XmlStructure;

class Punchout extends \Codeception\Module
{
    public static function getOciSetupRequestData()
    {
        return [
            "HOOK_URL" => "http://localhost:8899/simulator/cart/receive.php",
            "username" => "user_1",
            "password" => "user_1_pass",
        ];
    }

    public static function getCxmlSetupRequestData()
    {
        $username = 'cxml' . rand(100, 999);

        return <<<XML_DATA
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.007/cXML.dtd">
<cXML version="1.1.007" xml:lang="en-US" payloadID="486698.1561035135.895@localhost:8899" timestamp="2019-06-20UTC12:52:15+00:00">
    <Header>
        <From>
            <Credential domain="http://localhost:8899">
                <Identity>user_1</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="user_1_pass">
                <Identity>pass_1</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="http://localhost:8899">
                <Identity>user_1</Identity>
                <SharedSecret>user_1_pass</SharedSecret>
            </Credential>
            <UserAgent>Punchout Cloud Simulator 0.1.0</UserAgent>
        </Sender>
    </Header>
    <Request>
        <PunchOutSetupRequest operation="create">
            <BuyerCookie>poc5d0b817f59d4c</BuyerCookie>

            <Extrinsic name="FirstName">cXML</Extrinsic>
            <Extrinsic name="LastName">Tester</Extrinsic>
            <Extrinsic name="UserEmail">$username@punchoutcatalogs.net</Extrinsic>

            <Contact>
                <Name xml:lang="en-US">cXML Tester</Name>
                <Email>$username@punchoutcatalogs.net</Email>
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

}
