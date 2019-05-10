<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;

trait SnippetTrait
{
    /**
     * @var array|\Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer[]
     */
    protected $snippets = [];

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer $mapping
     *
     * @return $this
     */
    protected function registerSnippets(PunchoutCatalogMappingTransfer $mapping)
    {
        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object */
        foreach ($mapping->getObjects() as $object) {
            if ($object->getIsCustom()) {
                $this->snippets[$object->getName()] = $object;
            }
        }
        return $this;
    }

    /**
     * @param $xpath
     *
     * @return bool|string
     */
    protected function toSnippet($xpath)
    {
        $xpath = explode('/', $xpath);
        $snippet = end($xpath);

        if (!$snippet || strlen($snippet) < 2 || (strpos($snippet, '()') === false)) {
            return false;
        }
        $snippet = str_replace('()', '', $snippet);
        return trim($snippet);
    }

    /**
     * @todo: review it
     *
     * @param $xpath
     *
     * @return string
     */
    protected function toSnippetPath($xpath): string
    {
        $xpath = explode('/', $xpath);
        array_pop($xpath);//remove last element
        return implode('/', $xpath);
    }

    /**
     * @param string $name
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer|null
     */
    protected function getSnippet(string $name): ?PunchoutCatalogMappingObjectTransfer
    {
        return isset($this->snippets[$name]) ? $this->snippets[$name] : null;
    }

    /**
     * @param string $path
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer|null
     */
    protected function getSnippetFromPath(string $path): ?PunchoutCatalogMappingObjectTransfer
    {
        $snippetName = $this->toSnippet($path);
        $snippet = $snippetName ? $this->getSnippet($snippetName) : null;
        $snippetRelPath = $snippet ? $this->toSnippetPath($path) : null;

        $_snippet = null;
        if ($snippetRelPath && is_string($snippetRelPath)) {
            $_snippet = clone $snippet;
            $_snippet->setPath([$snippetRelPath]);
        }

        return $_snippet;
    }
}
