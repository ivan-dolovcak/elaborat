<?php
# Datoteka: htdocs/app/UserModel.php

# Klasa UserModel sadrži sve atribute i metode vezane za korisnika.
class UserModel
{
    # Korisničko ime: Samo ASCII znakovi, duljine između 4 i 30 znakova:
    const REGEX_VALID_USERNAME = "/^\w{4,30}$/";
    # Ime i prezime: bez brojeva, između 3 i 40 znakova:
    const REGEX_VALID_NAME = "/^\D{3,40}$/";
    # Između 8 i 50 znakova, barem 1 veliko slovo i barem 1 broj:
    const REGEX_VALID_PASSWORD = "/^(?=.*\d)(?=.*[A-Z]).{8,50}$/";
    const UPDATE_VARS = ["username", "email", "firstName", "lastName", ];

    static function signUp(string $username, string $email, string $password, string $firstName, string $lastName) : ?string
    {
        $query = "INSERT INTO `User`(`username`, `email`, `passwordHash`, `firstName`, `lastName`)
            VALUES(?, ?, ?, ?, ?)";

        # Uspostavljanje veze s bazom podataka.
        $DB = DB::getInstance();

        # Hashiranje lozinke.
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        # Izvršavanje insert upita.
        $errorMsg = $DB->execStmt($query, "sssss", $username, $email,
            $passwordHash, $firstName, $lastName);

        if (gettype($errorMsg) === "string")
            return $errorMsg;

        # Ako je definirana varijabla sesije userID, to znači da je korisnik prijavljen u aplikaciju.
        $_SESSION["userID"] = $DB->conn->insert_id;

        return null;
    }
}
