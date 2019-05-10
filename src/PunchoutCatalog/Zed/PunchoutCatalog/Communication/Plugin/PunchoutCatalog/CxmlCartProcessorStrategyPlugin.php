<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use SimpleXMLElement;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlCartProcessorStrategyPlugin extends AbstractCartProcessorStrategyPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
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
    ): PunchoutCatalogCartResponseTransfer {
        $punchoutCatalogCartRequestOptionsTransfer->requireProtocolData();
        $punchoutCatalogCartRequestOptionsTransfer->requirePunchoutCatalogConnection();

        (new ProtocolDataValidator())->validate(
            $punchoutCatalogCartRequestOptionsTransfer->getProtocolData(),
            false
        );

        $content = $this->prepareCxmlContent(
            $punchoutCatalogCartRequestTransfer,
            $punchoutCatalogCartRequestOptionsTransfer
        );

        return (new PunchoutCatalogCartResponseTransfer())
            ->setIsSuccess(true)
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_HTML)
            ->setContent($content);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
     *
     * @return string
     */
    protected function prepareCxmlContent(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): string {
        $connection = $punchoutCatalogCartRequestOptionsTransfer->getPunchoutCatalogConnection();

        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$connection->getCart()->getMapping()
        );

        $cXML = $this->getReturnHeader(
            $punchoutCatalogCartRequestTransfer,
            $punchoutCatalogCartRequestOptionsTransfer
        );

        $cXML = new SimpleXMLElement($cXML);
        $cXML = (new Encoder())->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer, $cXML);

        if ($connection->getCart()->getEncoding() == PunchoutConnectionConstsInterface::CXML_ENCODING_URLENCODED) {
            $cxmlFields = [
                'cxml-urlencoded' => $cXML->asXML(), //@todo: fix it
            ];
        } else {
            $cxmlFields = [
                'cxml-base64' => base64_encode($cXML->asXML()),
            ];
        }

        return $this->renderHtmlForm(
            $cxmlFields,
            $punchoutCatalogCartRequestOptionsTransfer->getProtocolData()->getCart()->getUrl()
        );
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
                $object->setPath(['/cXML/Message[1]/PunchoutOrderMessage[1]/ItemIn']);
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
        <PunchoutOrderMessage>
            <BuyerCookie>{$buyerCookie}</BuyerCookie>
            <PunchoutOrderMessageHeader operationAllowed="{$operationAllowed}" />
        </PunchoutOrderMessage>
    </Message>
</cXML>
EOF;
    }
}
