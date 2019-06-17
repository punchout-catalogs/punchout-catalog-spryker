<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use SimpleXMLElement;
use InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\AbstractCoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\SnippetTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\TransformationTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\DecoderInterface;

class Decoder extends AbstractCoder implements DecoderInterface
{
    // @todo Snippet is not used anywhere beside registering snippets
    use SnippetTrait;
    use TransformationTrait;

    /**
     * @var array
     */
    protected $document = [];

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\XmlUtil
     */
    protected $xmlUtil;

    public function execute(PunchoutCatalogMappingTransfer $mapping, $source): array
    {
        if (!($source instanceof SimpleXMLElement)) {
            throw new InvalidArgumentException('punchout-catalog.error.invalid.source.data');
        }

        $this->xmlUtil = new XmlUtil();

        $this->document = [];
        $this->snippets = [];
        $this->registerSnippets($mapping);

        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object */
        foreach ($mapping->getObjects() as $object) {
            if ($object->getIsCustom()) {
                continue;
            }

            if ($object->getIsMultiple()) {
                $_lines = [];
                
                if ($object->getPath()) {
                    foreach ($object->getPath() as $path) {
                        $multiLines = $this->get($source, $path);
        
                        //@todo: reduce code complexity
                        if ($multiLines && is_array($multiLines)) {
                            foreach ($multiLines as $lineSource) {
                                $_line = $this->map($object, $lineSource);
                                if (!empty($_line)) {
                                    $_lines[] = $_line;
                                }
                            }
                        }
                    }
                } else {
                    $_line = $this->map($object, $source);
                    if (!empty($_line)) {
                        $_lines[] = $_line;
                    }
                }

                $this->document[$object->getName()] = $_lines;
            } else {
                $this->document[$object->getName()] = $this->map($object, $source);
            }
        }

        return $this->document;
    }

    /**
     * @param $source
     * @param $path
     *
     * @return array|bool
     */
    public function get($source, $path)
    {
        return $this->xmlUtil->get($source, $path);
    }
}
