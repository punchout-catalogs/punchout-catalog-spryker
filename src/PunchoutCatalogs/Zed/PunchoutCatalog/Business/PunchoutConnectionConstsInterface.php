<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business;

interface PunchoutConnectionConstsInterface
{
    public const FORMAT_CXML = 'cxml';
    public const FORMAT_OCI = 'oci';

    public const DATA_FORMAT_XML = 'xml';
    public const DATA_FORMAT_ARRAY = 'array';

    public const CONTENT_TYPE_FORM_MULTIPART = 'multipart/form-data';
    public const CONTENT_TYPE_TEXT_XML = 'text/xml';
    public const CONTENT_TYPE_TEXT_HTML = 'text/html';
    public const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';

    public const ERROR_MISSING_REQUEST_PROCESSOR = 'punchout-catalog.error.missing-request-processor';
    public const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';
    public const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid.data';
    public const MESSAGE_CART_RETURN = 'punchout-catalog.cart.return.msg';
    
    public const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';

    public const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';

    public const CXML_ENCODING_BASE64 = 'base64';
    public const CXML_ENCODING_URLENCODED = 'urlencoded';
}
