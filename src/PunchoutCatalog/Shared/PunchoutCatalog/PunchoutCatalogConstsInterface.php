<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Shared\PunchoutCatalog;


class PunchoutCatalogConstsInterface
{
    public const IS_PUNCHOUT = 'is_punchout';
    public const PUNCHOUT_LOGIN_MODE = 'punchout_login_mode';

    public const CUSTOMER_LOGIN_MODE_SINGLE = 'single_user';
    public const CUSTOMER_LOGIN_MODE_DYNAMIC = 'dynamic_user';

    public const FORMAT_CXML = 'cxml';
    public const FORMAT_OCI = 'oci';

    public const CONTENT_TYPE_FORM_MULTIPART = 'multipart/form-data';
    public const CONTENT_TYPE_TEXT_XML = 'text/xml';
    public const CONTENT_TYPE_TEXT_HTML = 'text/html';
    public const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
}
