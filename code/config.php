<?php
# Datoteka: htdocs/app/config.php

declare(strict_types=1);

# Sintaksne greške u kodu se prikazuju samo u razvojnom okruženju, tj. ako je definirana Apache varijabla DEVELOPMENT.
if (isset($_SERVER["DEVELOPMENT"])) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
    ini_set("display_startup_errors", true);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

# U back-endu su svi datumi i vremena spremljeni u UTC vremenskoj zoni
date_default_timezone_set("UTC");

# Automatsko includeanje klasa:
spl_autoload_register(fn($className) => require "$className.php");

# Učitavanje korisničkih postavki:
if (! isset($_COOKIE[Preferences::COOKIE_NAME]))
    Preferences::savePreferences(Preferences::DEFAULT_PREFERENCES);

# Korisničke postavke su dostupne u ovoj globalnoj varijabli:
$preferences = Preferences::loadPreferences();

# Učitavanje jezika stranice:
require_once "lang_{$preferences["lang"]}.php";
