<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\InnerBrowser;
use Codeception\Util\Xml;
use Codeception\Util\XmlStructure;

class Punchout extends \Codeception\Module
{
    public static function getSetupRequestData()
    {
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
            <Extrinsic name="UserEmail">cxml3453432332434@punchoutcatalogs.net</Extrinsic>

            <Contact>
                <Name xml:lang="en-US">cXML Tester</Name>
                <Email>cxml3453432332434@punchoutcatalogs.net</Email>
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

    public function isCxml($xml)
    {
        $this->assertContains('<cXML', $xml, 'is cXML');
    }

    public function getBase64CxmlCartResponse()
    {
        /** @var InnerBrowser $module */
        $module = $this->getModule('PhpBrowser');
        $html = $module->_getResponseContent();
        $structure = new XmlStructure(Xml::toXml($html));
        return $structure->matchElement('#punchoutCartForm [name="cxml-base64"]')->getAttribute('value');
    }

    public function getAccessUrl()
    {
        $response = $this->getModule('REST')->response;
        $xml = Xml::toXml($response);
        $structure = new XmlStructure($xml);
        $url = $structure->matchElement('//cXML/Response/PunchOutSetupResponse/StartPage/URL')->textContent;
        return $url;
    }

}
