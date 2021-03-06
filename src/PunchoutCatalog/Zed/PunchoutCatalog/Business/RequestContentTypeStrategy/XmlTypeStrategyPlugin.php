<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestContentTypeStrategy;


use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;

class XmlTypeStrategyPlugin implements RequestContentTypeStrategyPluginInterface
{
    /**
     * @param $requestContentType
     * @return bool
     */
    public function isApplicable(?string $requestContentType)
    {
        return $requestContentType === 'xml';
    }

    /**
     * @param $requestContentType
     * @return string
     */
    public function getPunchoutCatalogContentType(?string $requestContentType)
    {
        return PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML;
    }
}
