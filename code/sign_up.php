<?php
# Datoteka: htdocs/app/forms/sign_up.php

require_once "config.php";
session_start();

# Definiranje stranica na koje će se preusmjeriti korisnika - prva ako je zahtjev uspješan, druga ako je došlo do neke greške.
if (isset($_GET["update"])) {
    $successPage = "/views/profile.phtml";
    $failurePage = "/views/profile.phtml";
} else {
    $successPage = "/views/index.phtml";
    $failurePage = "/views/sign_up.phtml";
}

$requiredPostVars = ["username", "email", "firstName", "lastName", ];
if (! isset($_GET["update"]))
    # Polje za lozinku je obavezno samo tokom registracije:
    $requiredPostVars[] = "password";

# Testiranje jesu li sva obavezna polja prisutna u POST zahtjevu. AKo nisu, preusmjerava se korisnika i prikazuje se greška.
if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || array_diff($requiredPostVars, array_keys($_POST)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    Util::redirect("$failurePage");
}

# Deklariranje sanitiziranih varijabli kraćeg naziva preko pomoćne funkcije (npr. umjesto $_POST["email"], pišem samo $email)
foreach ($requiredPostVars as $postVar)
    $$postVar = Util::sanitizeFormData($_POST[$postVar]);

# Validacija redom svih polja pomoću regex funkcije preg_match() i regex patterna definiranih kao javne konstante klase UserModel.
# Ako bilo koje polje nije validno, obavlja se redirekcija i prikazuje se greška.
if (! preg_match(UserModel::REGEX_VALID_USERNAME, $username)) {
    $_SESSION["formErrorMsg"] = LANG["invalidUsername"];
}
elseif (! preg_match(UserModel::REGEX_VALID_NAME, $firstName)
    || ! preg_match(UserModel::REGEX_VALID_NAME, $lastName))
{
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
elseif (! isset($_GET["update"])
    && ! preg_match(UserModel::REGEX_VALID_PASSWORD, $password))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPassword"];
}
elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)
    || ! checkdnsrr(substr($email, strpos($email, "@")+1)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidEmail"];
}
elseif (! isset($_GET["update"])) {
    # Testiranje jesu li korisničko ime ili e-mail adresa već zauzeti.
    $DB = DB::getInstance();
    if ($DB->isTaken("User", "username", $username))
        $_SESSION["formErrorMsg"] = LANG["usernameTakenError"];
    elseif ($DB->isTaken("User", "email", $email))
        $_SESSION["formErrorMsg"] = LANG["emailTakenError"];
}

if (isset($_SESSION["formErrorMsg"])) {
    Util::redirect("$failurePage");
}

if (isset($_GET["update"])) {
    # Ažuriranje korisničkih podataka.
    $user = UserModel::ctorLoad($_SESSION["userID"]);

    foreach (UserModel::UPDATE_VARS as $updateVar) {
        $user->$updateVar = $$updateVar;
    }

    $errorMsg = $user->update();
}
else {
    # Zapisivanje korisnika u bazu podataka.
    $errorMsg = UserModel::signUp($username, $email, $password,
        $firstName, $lastName);
}

if (isset($errorMsg))
    $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";

if (isset($_SESSION["formErrorMsg"]))
    Util::redirect("$failurePage");
else
    Util::redirect("$successPage");
