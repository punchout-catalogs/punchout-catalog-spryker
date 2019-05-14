<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseFieldTransfer;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;

use SimpleXMLElement;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlCartProcessorStrategyPlugin extends AbstractPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
{
    protected const CXML_VERSION = '1.2.021';

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): PunchoutCatalogCartResponseTransfer
    {
        $context = new PunchoutCatalogCartResponseContextTransfer();
        $context->setConnectionSessionId('fake_session_id');
        $response = (new PunchoutCatalogCartResponseTransfer())
            ->setIsSuccess(true)
            ->setContext($context);

        try {
            $punchoutCatalogCartRequestOptionsTransfer->requireProtocolData();
            $punchoutCatalogCartRequestOptionsTransfer->requirePunchoutCatalogConnection();
    
            (new ProtocolDataValidator())->validate(
                $punchoutCatalogCartRequestOptionsTransfer->getProtocolData(),
                false
            );
    
            $xml = $this->prepareXmlContent(
                $punchoutCatalogCartRequestTransfer,
                $punchoutCatalogCartRequestOptionsTransfer
            );
    
            $xml = $xml->asXML();
            
            $connection = $punchoutCatalogCartRequestOptionsTransfer->getPunchoutCatalogConnection();
            
            //The names cXML-urlencoded and cXML-base64 are case insensitive.
            if ($connection->getCart()->getEncoding() == PunchoutConnectionConstsInterface::CXML_ENCODING_URLENCODED) {
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
            
            $response->getContext()->setRawData($xml);
            return $response;
        } catch (\Exception $e) {
            $msg = PunchoutConnectionConstsInterface::ERROR_GENERAL;
            
            if (($e instanceof RequiredTransferPropertyException) || ($e instanceof InvalidArgumentException)) {
                $msg = $e->getMessage();
            }
            
            return $response->setIsSuccess(false)->addMessage(
                (new MessageTransfer())->setValue($msg)
            );
        }
    }
    
    /**
     * @param string $value
     *
     * @return string
     */
    protected function fixUrlencodedValue(string $value): string
    {
        $value = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
        $value = iconv('utf-8', 'us-ascii//TRANSLIT', $value);
        return $value;
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
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
     *
     * @return SimpleXMLElement
     */
    protected function prepareXmlContent(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): SimpleXMLElement
    {
        $connection = $punchoutCatalogCartRequestOptionsTransfer->getPunchoutCatalogConnection();
        
        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$connection->getCart()->getMapping()
        );

        $xml = $this->getReturnHeader(
            $punchoutCatalogCartRequestTransfer,
            $punchoutCatalogCartRequestOptionsTransfer
        );

        $xml = new SimpleXMLElement($xml);
    
        $xml = (new Encoder())->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer, $xml);
        
        return $xml;
    }

    /**
     * @param string $mapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    protected function convertToMappingTransfer(string $mapping): PunchoutCatalogMappingTransfer
    {
        $mappingTransfer = parent::convertToMappingTransfer($mapping);

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
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
     *
     * @return string
     */
    public function getReturnHeader(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): string {
        $ver = static::CXML_VERSION;

        $toCxml = $punchoutCatalogCartRequestOptionsTransfer->getProtocolData()->getCxmlToCredentials();
        $senderCxml = $punchoutCatalogCartRequestOptionsTransfer->getProtocolData()->getCxmlSenderCredentials();
        $cart = $punchoutCatalogCartRequestOptionsTransfer->getProtocolData()->getCart();

        $supDomain = htmlentities($toCxml->getDomain());
        $supId = htmlentities($toCxml->getIdentity());

        $senderDomain = htmlentities($senderCxml->getDomain());
        $senderId = htmlentities($senderCxml->getIdentity());

        $buyerCookie = htmlentities($cart->getBuyerCookie());
        $deploymentMode = htmlentities($cart->getDeploymentMode());

        $operationAllowed = 'create';

        return <<< EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="{$this->getPayloadId()}"
    timestamp="{$this->getTimestamp()}"
    xml:lang="{$punchoutCatalogCartRequestTransfer->getLocale()}"
    version="{$ver}"
>
    <Header>
        <From>
             <Credential domain="{$supDomain}">
                <Identity>{$supId}</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="{$senderDomain}">
                <Identity>{$senderId}</Identity>
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
}
