<?php

declare(strict_types=1);

/**
 * SSO Bridge - Redirects legacy/external SSO calls to the main router
 */

// If we are at the root, we just need to let index.php handle it.
// But if external systems call /auth_sso.php directly, we can handle it here.

// Bootstrap the application
require_once __DIR__ . '/index.php';
