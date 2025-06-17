<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<footer class="site-footer">
    <div class="footer-links">
        <a href="/v/impressum.php" class="footer-link">Impressum</a>
        <a href="/v/datenschutz.php" class="footer-link">Datenschutz</a>
        <a href="/v/help.php" class="footer-link">Hilfe</a>
        <a href="/v/support.php" class="footer-link">Support</a>
    </div>
    <div class="footer-contact" style="margin-top: 10px; font-size: 0.9em;">
        <p>Reitschulgasse 13, 8010 Graz / Austria</p>
        <p>0043 664 2525060</p>
        <p><a href="mailto:support@videomat.org" class="footer-link">support@videomat.org</a></p>
    </div>
</footer>