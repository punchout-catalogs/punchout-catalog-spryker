<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\ContentProcessor;

class CxmlContentProcessor implements CxmlContentProcessorInterface
{
    /**
     * @param string $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(string $content): array
    {
        $xml = $this->findXml($content);

        return [
            'cxml_to_credentials' => [
                'identity' => $this->findFirstValueByXPath($xml, 'Header/To/Credential/Identity'),
                'domain'   => $this->findFirstValueByXPath($xml, 'Header/To/Credential/@domain'),
            ],
            'cxml_sender_credentials' => [
                'identity' => $this->findFirstValueByXPath($xml, 'Header/Sender/Credential/Identity'),
                'domain'   => $this->findFirstValueByXPath($xml, 'Header/Sender/Credential/@domain'),
                'sharedSecret' => $this->findFirstValueByXPath($xml, 'Header/Sender/Credential/SharedSecret'),
            ],
            'cart' => [
                'url' => $this->findFirstValueByXPath($xml, 'Request/PunchOutSetupRequest/BrowserFormPost/URL'),
                'buyerCookie' => $this->findFirstValueByXPath($xml, 'Request/PunchOutSetupRequest/BuyerCookie'),
                'deploymentMode' => $this->findFirstValueByXPath($xml, 'Request/@deploymentMode') ?? 'production',
                'operation' => $this->findFirstValueByXPath($xml, 'Request/PunchOutSetupRequest/@operation') ?? 'create',
            ]
        ];
    }

    /**
     * @param string $content
     *
     * @return string|null
     */
    public function fetchOperation(string $content): ?string
    {
        $xml = $this->findXml($content);
        if ($xml && $xml->Request && $xml->Request->children()[0] && $xml->Request->children()[0]->getName()) {
            return "request/" . strtolower($xml->Request->children()[0]->getName());
        }
        
        return null;
    }

    /**
     * @param string $content
     *
     * @return bool
     */
    public function isCXmlContent(string $content): bool
    {
        $xml = $this->findXml($content);
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
    protected function findXml(string $content): ?\SimpleXMLElement
    {
        $xml = new \SimpleXMLElement($content);

        return $xml ? $xml : null;
    }
    
    /**
     * @param \SimpleXMLElement $XMLElement
     * @param string            $xpath
     *
     * @return string|null
     */
    protected function findFirstValueByXPath(\SimpleXMLElement $XMLElement, string $xpath): ?string
    {
        $val = (string)current($XMLElement->xpath($xpath));
        return $val !== '' ? $val : null;
    }
}
