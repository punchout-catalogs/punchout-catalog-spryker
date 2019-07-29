<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
 */
class OciCartProcessorStrategyPlugin extends AbstractPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
    ): PunchoutCatalogCartResponseTransfer
    {
        $response = (new PunchoutCatalogCartResponseTransfer())
            ->setIsSuccess(true);

        $punchoutCatalogCartRequestTransfer->requireContext();

        $context = (new PunchoutCatalogCartResponseContextTransfer())->fromArray(
            $punchoutCatalogCartRequestTransfer->getContext()->toArray(), true
        );
        $response->setContext($context);

        $punchoutCatalogCartRequestContextTransfer = $punchoutCatalogCartRequestTransfer->getContext()
            ->requireProtocolData()
            ->requirePunchoutCatalogConnection();

        ($this->getFactory()->createOciProtocolDataValidator())->validate($punchoutCatalogCartRequestContextTransfer->getProtocolData());

        $fields = $this->prepareOciContent(
            $punchoutCatalogCartRequestTransfer,
            $punchoutCatalogCartRequestContextTransfer
        );

        foreach ($fields as $fieldName => $fieldValue) {
            $response->addResponseField(
                (new PunchoutCatalogCartResponseFieldTransfer())
                    ->setName($fieldName)
                    ->setValue($this->fixOciValue((string)$fieldValue))
            );
        }

        $response->getContext()->setRawData($punchoutCatalogCartRequestTransfer->toArray());
        $response->getContext()->setContent(json_encode($fields, JSON_PRETTY_PRINT));
        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer $punchoutCatalogCartRequestContextTransfer
     *
     * @return array
     */
    protected function prepareOciContent(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestContextTransfer $punchoutCatalogCartRequestContextTransfer
    ): array
    {
        $connection = $punchoutCatalogCartRequestContextTransfer->getPunchoutCatalogConnection();

        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$connection->getCart()->getMapping()
        );

        return $this->getFactory()->createOciEncoder()->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer);
    }

    /**
     * @param string $mapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    protected function convertToMappingTransfer(string $mapping): PunchoutCatalogMappingTransfer
    {
        $mappingTransfer = $this->getFacade()->convertToMappingTransfer($mapping);

        foreach ($mappingTransfer->getObjects() as $object) {
            if ($object->getName() == 'cart_item') {
                $object->setIsMultiple(true);
            }
        }

        return $mappingTransfer;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function fixOciValue(string $value): string
    {
        $value = preg_replace('/\s\s+|\t/', ' ', $value);
        //return htmlentities($value, ENT_QUOTES);
        return htmlspecialchars($value, ENT_QUOTES);
    }
}
