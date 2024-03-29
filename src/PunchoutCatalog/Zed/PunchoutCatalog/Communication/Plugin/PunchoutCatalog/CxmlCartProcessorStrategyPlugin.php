<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;
use SimpleXMLElement;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @see: http://xml.cxml.org/current/ReleaseNotes.html
 *
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
 */
class CxmlCartProcessorStrategyPlugin extends AbstractPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
{
    protected const CXML_VERSION = '1.2.023';//1.2.021

    protected const CXML_ENCODING_BASE64 = 'base64';
    protected const CXML_ENCODING_URLENCODED = 'url-encoded';

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
    ): PunchoutCatalogCartResponseTransfer
    {
        $response = (new PunchoutCatalogCartResponseTransfer())
            ->setIsSuccess(true);

        $punchoutCatalogCartRequestTransfer->requireContext();

        $context = (new PunchoutCatalogCartResponseContextTransfer())->fromArray(
            $punchoutCatalogCartRequestTransfer->getContext()->toArray(), true
        );
        $response->setContext($context);

        $context = $punchoutCatalogCartRequestTransfer->getContext()
            ->requireProtocolData()
            ->requirePunchoutCatalogConnection();

        $this->getFactory()->createXmlProtocolDataValidator()->validate(
            $context->getProtocolData(),
            false
        );

        $xml = $this->prepareXmlContent($punchoutCatalogCartRequestTransfer);

        $xml = $xml->asXML();

        $connection = $context->getPunchoutCatalogConnection();

        //The names cXML-urlencoded and cXML-base64 are case insensitive.
        if ($connection->getCart()->getEncoding() == self::CXML_ENCODING_URLENCODED) {
            $response->addResponseField(
                (new PunchoutCatalogCartResponseFieldTransfer())
                    ->setName('cxml-urlencoded')
                    ->setValue($this->fixUrlencodedValue($xml))
            );
        } else {
            $response->addResponseField(
                (new PunchoutCatalogCartResponseFieldTransfer())
                    ->setName('cxml-base64')
                    ->setValue($this->fixBase64Value($xml))
            );
        }

        $response->getContext()->setRawData($punchoutCatalogCartRequestTransfer->toArray());
        $response->getContext()->setContent($xml);
        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return SimpleXMLElement
     */
    protected function prepareXmlContent(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): SimpleXMLElement
    {
        $context = $punchoutCatalogCartRequestTransfer->getContext();
        $connection = $context->getPunchoutCatalogConnection();

        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$connection->getCart()->getMapping()
        );

        $xml = $this->getReturnHeader($punchoutCatalogCartRequestTransfer);

        $xml = new SimpleXMLElement($xml);

        $xml = $this->getFactory()->createXmlEncoder()->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer, $xml);

        return $xml;
    }

    /**
     * @param string $mapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    protected function convertToMappingTransfer(string $mapping): PunchoutCatalogMappingTransfer
    {
        $mappingTransfer = $this->getFacade()->convertToMappingTransfer($mapping);

        foreach ($mappingTransfer->getObjects() as $object) {
            if ($object->getName() == 'cart_item') {
                $object->setIsMultiple(true);
                $object->setPath(['/cXML/Message[1]/PunchOutOrderMessage[1]/ItemIn']);
            }
        }

        return $mappingTransfer;
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return string
     */
    public function getReturnHeader(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): string
    {
        $ver = static::CXML_VERSION;

        $context = $punchoutCatalogCartRequestTransfer->getContext();

        $toCxml = $context->getProtocolData()->getCxmlToCredentials();
        $buyerCxml = $context->getProtocolData()->getCxmlBuyerCredentials();

        $cart = $context->getProtocolData()->getCart();

        $supDomain = htmlentities($toCxml->getDomain());
        $supId = htmlentities($toCxml->getIdentity());

        $buyerDomain = 'NetworkId';
        $buyerId = 'Tester';

        if ($buyerCxml) {
            $buyerDomain = $buyerCxml->getDomain() ? htmlentities($buyerCxml->getDomain()) : $buyerDomain;
            $buyerId = $buyerCxml->getIdentity() ? htmlentities($buyerCxml->getIdentity()) : $buyerId;
        }

        $buyerCookie = htmlentities($cart->getBuyerCookie());
        $deploymentMode = htmlentities($cart->getDeploymentMode());

        $operationAllowed = 'create';

        $storeName = $this->getFactory()->getStoreFacade()->getCurrentStore()->getName();

        $yvesPayloadId = $this->getFacade()->getYvesPayloadId($storeName);
        $timestamp = $this->getFacade()->getTimestamp();
        return <<< EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="{$yvesPayloadId}"
    timestamp="{$timestamp}"
    xml:lang="{$context->getLocale()}"
    version="{$ver}"
>
    <Header>
        <From>
             <Credential domain="{$supDomain}">
                <Identity>{$supId}</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="{$buyerDomain}">
                <Identity>{$buyerId}</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="{$supDomain}">
                <Identity>{$supId}</Identity>
            </Credential>
            <UserAgent>Spryker Punchout cXML Gateway</UserAgent>
        </Sender>
    </Header>
    <Message deploymentMode="{$deploymentMode}">
        <PunchOutOrderMessage>
            <BuyerCookie>{$buyerCookie}</BuyerCookie>
            <PunchOutOrderMessageHeader operationAllowed="{$operationAllowed}" />
        </PunchOutOrderMessage>
    </Message>
</cXML>
EOF;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function fixUrlencodedValue(string $value): string
    {
        $value = htmlentities($value, ENT_QUOTES, "utf-8");
        //$value = htmlspecialchars($value, ENT_QUOTES, "utf-8");
        return iconv('utf-8', 'us-ascii//TRANSLIT', $value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function fixBase64Value(string $value): string
    {
        return base64_encode($value);
    }
}
