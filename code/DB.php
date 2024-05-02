<?php
# Datoteka: htdocs/app/DB.php

require "sql_auth.php";

class DB
{
    private static self $obj; # Singleton instanca
    public MySQLi $conn;      # MySQLi objekt - veza s bazom podataka


    # Prilikom inicijalizaciju singleton instance, uspostavlja se veza s bazom podataka.
    private function __construct()
    {
        # Konstruktoru MySQLi se prosljeđuju informacije potrebne za uspostavu veze: korisničko ime, host, lozinka i ime baze.
        # Ove konstante su definirane u zasebnoj datoteci sql_auth.php
        $this->conn = new MySQLi(SQL_HOSTNAME, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
    }

    # Singleton klasa - moguće je napraviti samo jednu instancu ove klase, čime se sprečava redundantno uspostavljanje i prekidanje veze s bazom podataka (efikasnost).
    static function getInstance() : self
    {
        if (! isset(self::$obj))
            self::$obj = new self;

        return self::$obj;
    }

    # Ova metoda izvršava dani SQL upit pomoću tzv. prepared statementa - još jedan sigurnosni sloj.
    function execStmt(string $query, ?string $types, mixed ...$queryArgs) : MySQLi_result|false|string
    {
        try {
            $stmt = $this->conn->prepare($query);
            if (isset($types))
                $stmt->bind_param($types, ...$queryArgs);
            $stmt->execute();
            return $stmt->get_result();
        }
        catch (MySQLi_SQL_exception $e) {
            return $e->getMessage();
        }
    }

    # Ova metoda testira je li vrijednost za dani stupac u tablici zauzeta.
    # Koristi se kasnije za provjeru dostupnosti korisničkog imena i e-mail adrese.
    function isTaken(string $table, string $column, string $value) : bool|string
    {
        $query = "SELECT `$column`
            FROM `$table`
            WHERE `$column` = ?";

        $DB = self::getInstance();
        $result = $DB->execStmt($query, "s", $value);

        if (gettype($result) === "string")
            return $result;
        elseif ($result->num_rows > 0)
            return true;
        else
            return false;
    }

    # Prije uništavanja singleton instance, prekida se veza s bazom podataka.
    function __destruct()
    {
        $this->conn->close();
    }
}
