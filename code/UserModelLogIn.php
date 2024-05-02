<?php
class UserModel
{
    # Vraća null ako je prijava uspješna, u suprotnom vraća false.
    static function logIn(string $usernameOrEmail, string $password) : string|false|null
    {
        $query = "SELECT `ID`, `passwordHash`
            FROM `User`
            WHERE `username` = ? OR `email` = ?";

        $DB = DB::getInstance();

        $result = $DB->execStmt($query, "ss", $usernameOrEmail, $usernameOrEmail);
        if (gettype($result) === "string")
            return $result; # Greška baze podataka.

        $resultRow = $result->fetch_assoc();
        $ID = $resultRow["ID"];
        $passwordHash = $resultRow["passwordHash"];

        # Autentikacija - uspoređuje se hashirana lozinka iz baze s hashiranom lozinkom podnesenom u obrascu za prijavu.
        if (! $ID || ! password_verify($password, $passwordHash))
            return false;

        # Spremanjem ID-a korisnika u varijablu sesije, aplikacija zna da je korisnik/ca s tim ID-jem prijavljen/a.
        $_SESSION["userID"] = $ID;

        # Ažuriranje stupca datuma i vremena zadnje prijave.
        $query = "UPDATE `User`
            SET `lastLoginTime` = utc_timestamp()
            WHERE `ID` = ?";

        $DB->execStmt($query, "i", $_SESSION["userID"]);

        return null;
    }
}
