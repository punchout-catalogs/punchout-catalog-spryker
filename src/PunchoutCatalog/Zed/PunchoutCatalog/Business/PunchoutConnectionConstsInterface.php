<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

interface PunchoutConnectionConstsInterface
{
    public const FORMAT_CXML = 'cxml';
    public const FORMAT_OCI = 'oci';

    public const DATA_FORMAT_XML = 'xml';
    public const DATA_FORMAT_ARRAY = 'array';
    
    public const CONTENT_TYPE_FORM = 'form';
    public const CONTENT_TYPE_XML = 'xml';
    
    public const CONTENT_TYPE_FORM_MULTIPART = 'multipart/form-data';
    public const CONTENT_TYPE_TEXT_XML = 'text/xml';
    public const CONTENT_TYPE_TEXT_HTML = 'text/html';
    public const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
    
    public const ERROR_GENERAL = 'punchout-catalog.error.general';
    public const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';
    public const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';
    public const ERROR_UNEXPECTED = 'punchout-catalog.error.unexpected';
    public const ERROR_MISSING_LOGIN_MODE = 'punchout-catalog.error.missing-login-mode';
    public const ERROR_MISSING_COMPANY_BUSINESS_UNIT = 'punchout-catalog.error.missing-company-business-unit';
    public const ERROR_MISSING_COMPANY_USER = 'punchout-catalog.error.missing-company-user';

    public const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';

    public const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';
    
    public const CXML_ENCODING_BASE64 = 'base64';
    public const CXML_ENCODING_URLENCODED = 'urlencoded';
    
    public const CUSTOMER_LOGIN_MODE_SINGLE = 'single_user';
    public const CUSTOMER_LOGIN_MODE_DYNAMIC = 'dynamic_user';
}
