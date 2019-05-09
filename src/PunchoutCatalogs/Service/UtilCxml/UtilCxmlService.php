<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Service\UtilCxml;

use Spryker\Service\Kernel\AbstractService;

/**
 * @method \PunchoutCatalogs\Service\UtilCxml\UtilCxmlFactory getFactory()
 */
class UtilCxmlService extends AbstractService implements UtilCxmlServiceInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(string $content): array
    {
        $xml = $this->getXml($content);

        return [
            'cxml_to_credentials' => [
                'identity' => $this->single($xml, 'Header/To/Credential/Identity'),
                'domain'   => $this->single($xml, 'Header/To/Credential/@domain'),
            ],
            'cxml_sender_credentials' => [
                'identity' => $this->single($xml, 'Header/Sender/Credential/Identity'),
                'domain'   => $this->single($xml, 'Header/Sender/Credential/@domain'),
                'sharedSecret' => $this->single($xml, 'Header/Sender/Credential/SharedSecret'),
            ],
            'cart' => [
                'url' => $this->single($xml, 'Request/PunchOutSetupRequest/BrowserFormPost/URL'),
                'buyerCookie' => $this->single($xml, 'Request/PunchOutSetupRequest/BuyerCookie'),
                'deploymentMode' => $this->single($xml, 'Request/@deploymentMode') ?? 'production',
                'operation' => $this->single($xml, 'Request/PunchOutSetupRequest/@operation') ?? 'create',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return string|null
     */
    public function getOperation(string $content): string
    {
        $xml = $this->getXml($content);
        if (!($xml instanceof \SimpleXMLElement)) {
            return null;
        }
        
        if ($xml->Request && $xml->Request->children()[0] && $xml->Request->children()[0]->getName()) {
            return "request/" . strtolower($xml->Request->children()[0]->getName());
        }
        
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return bool
     */
    public function isCXml(string $content): bool
    {
        $xml = $this->getXml($content);
        if ($xml && strtolower($xml->getName()) === 'cxml') {
            return true;
        }

        return false;
    }
    
    /**
     * @param string $content
     *
     * @return null|\SimpleXMLElement
     */
    protected function getXml(string $content): ?\SimpleXMLElement
    {
        //@Karoly can we cache it? (yeah ... stateless, but we load it 3 times instead of 1 just ot check some values)
        $xml = new \SimpleXMLElement($content);
        return $xml ? $xml : null;
    }
    
    /**
     * @param \SimpleXMLElement $XMLElement
     * @param string            $xpath
     *
     * @return string
     */
    protected function single(\SimpleXMLElement $XMLElement, string $xpath): string
    {
        return (string)current($XMLElement->xpath($xpath));
    }
}
