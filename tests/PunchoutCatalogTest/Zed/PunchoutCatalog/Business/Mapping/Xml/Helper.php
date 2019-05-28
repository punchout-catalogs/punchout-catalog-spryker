<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;

trait Helper
{
    /**
     * @return PunchoutCatalogMappingTransfer
     */
    protected function createMapping(): PunchoutCatalogMappingTransfer
    {
        return (new PunchoutCatalogMappingTransfer())
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('first_name')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'FirstName\']']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('last_name')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'LastName\']']))
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('email')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name=\'UserEmail\']']))
                    ->setName('customer')
            )
            ->addObject(
                (new PunchoutCatalogMappingObjectTransfer())
                    ->addField((new PunchoutCatalogMappingObjectFieldTransfer())
                        ->setName('internal_id')
                        ->setPath(['/cXML/Request[1]/PunchOutSetupRequest[1]/ItemOut/ItemID[1]/SupplierPartAuxiliaryID']))
                    ->setName('cart_item')
            );
    }
}
