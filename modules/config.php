<?php
// Basis-URL zur Verwendung in Redirects oder Linkaufbau
define('BASE_URL', '/v');

// Standard-Theme (kann von Session oder GET überschrieben werden)
define('DEFAULT_THEME', 'theme_first');

// Seitenpfade (zentral steuerbar)
define('LOGIN_PAGE', BASE_URL . '/login/login.php');
define('REGISTER_PAGE', BASE_URL . '/login/register.php');
define('FORGOT_PAGE', BASE_URL . '/login/forgot_password.php');
define('RESET_PAGE', BASE_URL . '/login/reset_password.php');

define('USER_DASHBOARD', BASE_URL . '/user/dashboard.php');
define('ADMIN_DASHBOARD', BASE_URL . '/admin/admin_dashboard.php');
define('LOGOUT_PAGE', BASE_URL . '/login/logout.php');

// E-Mail-Versand: Absenderadresse
define('MAIL_FROM', 'support@videomat.org');

// Token-Gültigkeit für Passwort-Zurücksetzen (in Sekunden)
define('RESET_TOKEN_VALIDITY', 3600);

// Timeout für automatische Abmeldung bei Inaktivität (in Sekunden)
define('SESSION_TIMEOUT', 900);

// Debug-Modus (false = deaktiviert, true = Fehler anzeigen)
define('DEBUG_MODE', true);

// Debug-Einstellungen
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
