<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

interface PunchoutConnectionConstsInterface
{
    // @todo move this to Shared.Config
    public const FORMAT_CXML = 'cxml';
    // @todo move this to Shared.Config
    public const FORMAT_OCI = 'oci';

    /**
     * @deprecated
     */
    public const DATA_FORMAT_XML = 'xml';
    /**
     * @deprecated
     */
    public const DATA_FORMAT_ARRAY = 'array';

    /**
     * @deprecated
     */
    public const CONTENT_TYPE_FORM = 'form';

    /**
     * @deprecated
     */
    public const CONTENT_TYPE_XML = 'xml';

    // @todo move this to Shared.Config
    public const CONTENT_TYPE_FORM_MULTIPART = 'multipart/form-data';
    // @todo move this to Shared.Config
    public const CONTENT_TYPE_TEXT_XML = 'text/xml';
    // @todo move this to Shared.Config
    public const CONTENT_TYPE_TEXT_HTML = 'text/html';
    // @todo move this to Shared.Config
    public const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';

    // @todo move these to the related file
    public const ERROR_GENERAL = 'punchout-catalog.error.general';
    // @todo move these to the related files - and connect the pairs
    public const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';
    // @todo move these to the related files - and connect the pairs
    public const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';
    // @todo move these to the related file
    public const ERROR_UNEXPECTED = 'punchout-catalog.error.unexpected';

    /**
     * @deprecated
     */
    public const ERROR_MISSING_LOGIN_MODE = 'punchout-catalog.error.missing-login-mode';
    // @todo move these to the related file
    public const ERROR_MISSING_COMPANY_BUSINESS_UNIT = 'punchout-catalog.error.missing-company-business-unit';
    // @todo move these to the related files
    public const ERROR_MISSING_COMPANY_USER = 'punchout-catalog.error.missing-company-user';
    // @todo move these to the related files
    public const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';
    // @todo move these to the related files - and connect the pairs
    public const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';

    /**
     * @deprecated
     */
    public const CXML_ENCODING_BASE64 = 'base64';
    // @todo move these to the related file
    public const CXML_ENCODING_URLENCODED = 'urlencoded';

    /**
     * @deprecated
     */
    public const CUSTOMER_LOGIN_MODE_SINGLE = 'single_user';
    // @todo move these to the related file
    public const CUSTOMER_LOGIN_MODE_DYNAMIC = 'dynamic_user_creation';

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
