<?php
class Preferences
{
    # Ime kolačića:
    const COOKIE_NAME = "examEnginePreferences";
    # Zadane postavke:
    const DEFAULT_PREFERENCES = [
        "theme" => "light",
        "lang" => "en",
        "accentColor" => "#00AA00",
    ];
    # Za validaciju postavki:
    const VALID_PREFERENCES = [
        "theme" => ["dark", "light", ],
        "lang" => ["en", "hr", ],
        "reset" => null,
        "accentColor" => null,
    ];


    # Privatni konstruktor - nemoguće je instancirati klasu:
    private function __construct() {}

    static function savePreferences($preferences) : void
    {
        # 3. argument - rok trajanja kolačića, 4. argument: putanja u kojoj je kolačić dostupan (/ znači cijelo web sjedište)
        setcookie(self::COOKIE_NAME, json_encode($preferences),
            strtotime("+1 year"), "/");
    }

    # Učitavanje postavki u asocijativno globalno polje:
    static function loadPreferences() : array
    {
        return json_decode($_COOKIE[self::COOKIE_NAME], associative:true);
    }
}
