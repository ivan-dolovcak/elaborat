// Datoteka: htdocs/static/js/form_validation.js

const mainForm = document.forms[0];

// Funkcija za slanje zahtjeva API-ju aplikacije pomoću JS fetch() API-ja.
async function apiGet(requestType, value)
{
    value = encodeURIComponent(value);

    const baseURL = "/app/api/form_validation.php";
    const requestURL = `${baseURL}?request=${requestType}&value=${value}`;

    const request = await fetch(requestURL);
    return request.text();
}

async function validateInput(input)
{
    const errorMsgElement = mainForm.getElementsByClassName("form-error-msg")[0];

    // Specijalna provjera dostupnosti e-mail adrese i korisničkog imena korisnika.
    if (input.name === "email" && input.value !== input.defaultValue) {
        const validationMessage = await apiGet("isUserEmailTaken", input.value);
        input.setCustomValidity(validationMessage);
    }
    else if (input.name === "username" && input.value !== input.defaultValue) {
        const validationMessage = await apiGet("isUsernameTaken", input.value);
        input.setCustomValidity(validationMessage);
    }

    // Prikaz greške/traženog formata na dnu obrasca:
    if (! input.checkValidity() && input.value) {
        if (input.validity.customError)
            errorMsgElement.innerText = input.validationMessage;
        else
            errorMsgElement.innerText = input.title;
    }
    else {
        input.setCustomValidity("");
        errorMsgElement.innerText = "";
    }
}

// Svakom polju na formi se dojdeljuje gornja funkcija za validaciju koja se pokreće 200 ms nakon zadnjeg pritiska tipke
for (const input of mainForm.elements) {
    if (input.type === "hidden")
        continue;
    input.addEventListener("keyup", () => {
        setTimeout(async() => validateInput(input), 200);
    });
    input.addEventListener("focus", async() =>
        validateInput(input)
    );
    input.addEventListener("blur", () => {
        mainForm.getElementsByClassName("form-error-msg")[0].innerText = "";
    });
}
