<?php
# Datoteka: htdocs/app/DB.php

class Util
{
    # Ova metoda služi za dohvaćanje poruke greške (ako ta greška postoji).
    # U kodu se na raznim mjestima varijabli sesije formErrorMsg dodjeljuje neka poruka greške, koja se kasnije prikazuje korisniku.
    public static function getFormError() : ?string
    {
        $errMsg = $_SESSION["formErrorMsg"] ?? null;
        unset($_SESSION["formErrorMsg"]);
        return $errMsg;
    }

    # Ova metoda služi za sanitizaciju podataka poslanih iz obrasca.
    public static function sanitizeFormData(string $data) : ?string
    {
        $sanitizedData = htmlspecialchars(stripslashes(trim($data)));
        if (empty($sanitizedData))
            return null;
        return $sanitizedData;
    }

    # Ova metoda obavlja redirekciju na dani URL.
    public static function redirect(string $URL) : void
    {
        header("Location: $URL");
        die;
    }
}
