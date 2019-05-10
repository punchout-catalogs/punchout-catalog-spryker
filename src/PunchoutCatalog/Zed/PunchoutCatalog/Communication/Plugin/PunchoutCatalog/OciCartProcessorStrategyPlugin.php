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
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Encoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci\ProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class OciCartProcessorStrategyPlugin extends AbstractCartProcessorStrategyPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
{
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
            $punchoutCatalogCartRequestOptionsTransfer->getProtocolData()
        );

        $content = $this->prepareOciContent(
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
    protected function prepareOciContent(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): string {
        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$punchoutCatalogCartRequestOptionsTransfer->getPunchoutCatalogConnection()->getCart()->getMappingCart()
        );

        $ociFields = (new Encoder())->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer);

        return $this->renderHtmlForm(
            $ociFields ?? [], //can be empty when we cancel cart
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
            }
        }

        return $mappingTransfer;
    }
}
