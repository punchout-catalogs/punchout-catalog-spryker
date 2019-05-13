<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseFieldTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Encoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci\ProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class OciCartProcessorStrategyPlugin extends AbstractPlugin implements PunchoutCatalogCartProcessorStrategyPluginInterface
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
    ): PunchoutCatalogCartResponseTransfer
    {
        $response = (new PunchoutCatalogCartResponseTransfer())
            ->setIsSuccess(true)
            ->setContext(new PunchoutCatalogCartResponseContextTransfer());
    
        try {
            $punchoutCatalogCartRequestOptionsTransfer->requireProtocolData();
            $punchoutCatalogCartRequestOptionsTransfer->requirePunchoutCatalogConnection();
        
            (new ProtocolDataValidator())->validate(
                $punchoutCatalogCartRequestOptionsTransfer->getProtocolData(),
                false
            );
    
            $fields = $this->prepareOciContent(
                $punchoutCatalogCartRequestTransfer,
                $punchoutCatalogCartRequestOptionsTransfer
            );
    
            foreach ($fields as $fieldName => $fieldValue) {
                $response->addResponseField(
                    (new PunchoutCatalogCartResponseFieldTransfer())
                        ->setName($fieldName)
                        ->setValue($fieldValue)//@todo: fix `fieldValue`
                );
            }
            $response->getContext()->setRawData($fields);
            return $response;
        } catch (\Exception $e) {
            $msg = PunchoutConnectionConstsInterface::ERROR_GENERAL;
        
            if (($e instanceof RequiredTransferPropertyException) || ($e instanceof InvalidArgumentException)) {
                $msg = $e->getMessage();
            }
            $msg = $e->getMessage();
            return $response->addMessage(
                (new MessageTransfer())->setValue($msg)
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
     *
     * @return array
     */
    protected function prepareOciContent(
        PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer,
        PunchoutCatalogCartRequestOptionsTransfer $punchoutCatalogCartRequestOptionsTransfer
    ): array
    {
        $connection = $punchoutCatalogCartRequestOptionsTransfer->getPunchoutCatalogConnection();
        
        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$connection->getCart()->getMapping()
        );

        return (new Encoder())->execute($mappingTransfer, $punchoutCatalogCartRequestTransfer);
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
