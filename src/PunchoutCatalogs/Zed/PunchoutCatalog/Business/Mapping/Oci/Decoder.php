<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Oci;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\AbstractCoder;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\SnippetTrait;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\TransformationTrait;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\DecoderInterface;

class Decoder extends AbstractCoder implements DecoderInterface
{
    use SnippetTrait;
    use TransformationTrait;

    /**
     * @var array
     */
    protected $document = [];

    public function execute(PunchoutCatalogMappingTransfer $mapping, $source)
    {
        if (!$this->validate($source)) {
            throw new InvalidArgumentException('Invalid source data');
        }

        $this->document = [];
        $this->snippets = [];
        $this->registerSnippets($mapping);

        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object */
        foreach ($mapping->getObjects() as $object) {
            if ($object->getIsCustom()) {
                continue;
            }

            $this->document[$object->getName()] = $this->map($object, $source);
        }

        return $this->document;
    }

    /**
     * @param $source
     *
     * @return bool
     */
    protected function validate($source): bool
    {
        return is_array($source);
    }

    /**
     * @param $source
     * @param $path
     *
     * @return array|bool
     */
    protected function get($source, $path)
    {
        if (!$this->validate($source)) {
            return false;
        }

        $result = isset($source[$path]) ? $source[$path] : null;

        if (empty($result)) {
            return false;
        }

        return is_array($result) ? $result : [$result];
    }
}
