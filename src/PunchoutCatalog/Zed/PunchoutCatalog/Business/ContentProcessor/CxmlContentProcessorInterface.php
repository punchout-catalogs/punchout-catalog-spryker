<?php

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\ContentProcessor;

interface CxmlContentProcessorInterface
{
    /**
     * @param string $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(string $content): array;

    /**
     * @param string $content
     *
     * @return string|null
     */
    public function getOperation(string $content): ?string;

    /**
     * @param string $content
     *
     * @return bool
     */
    public function isCXmlContent(string $content): bool;
}
