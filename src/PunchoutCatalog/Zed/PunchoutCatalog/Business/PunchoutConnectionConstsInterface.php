<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

interface PunchoutConnectionConstsInterface
{
    // @todo move these to the related files - and connect the pairs
    public const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';
    // @todo move these to the related files - and connect the pairs
    public const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';
    // @todo move these to the related file
    public const ERROR_UNEXPECTED = 'punchout-catalog.error.unexpected';


    // @todo move these to the related file
    public const ERROR_MISSING_COMPANY_BUSINESS_UNIT = 'punchout-catalog.error.missing-company-business-unit';
    // @todo move these to the related files
    public const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';
    // @todo move these to the related files - and connect the pairs
    public const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';

    // @todo move these to the related file
    public const BUNDLE_MODE_SINGLE = 'single';
    // @todo move these to the related file
    public const BUNDLE_MODE_COMPOSITE = 'composite';

    // @todo move these to the related file
    public const BUNDLE_COMPOSITE_PRICE_LEVEL = 'groupLevel';
    // @todo move these to the related file
    public const BUNDLE_COMPOSITE_ITEM_TYPE = 'composite';
    // @todo move these to the related file
    public const BUNDLE_CHILD_ITEM_TYPE = 'item';
}
