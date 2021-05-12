{
    function checkName(name) {
        if (name.value.match(/\d/) || name.value.length < 2 || name.value.length > 63) {
            name.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">The input must contain at least 2 characters and no number</div>"
        } else {
            name.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }
    }

    function invalidName(id) {
        let first_name = document.getElementById(id)
        checkName(first_name)
    }

    function invalidPassword(id) {

        let password = document.getElementById(id)
        let regExString = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\\d]{8,}$/'
        let errorMsg = 'Your password must contain at least one number, one uppercase letter and one lowercase letter'
        //checkMatch(password, regExString, errorMsg)

        if (!password.value.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/)) {
            password.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">Your password must contain at least one number, one uppercase letter and one lowercase letter</div>"
        } else {
            password.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }
    }

    function matchPasswords(passwordId, rPasswordId) {
        let password = document.getElementById(passwordId)
        let rPassword = document.getElementById(rPasswordId)
        if (password.value !== rPassword.value) {
            rPassword.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">Your passwords doesnt match</div>"
        } else {
            rPassword.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }
    }

    function invalidNumber(id) {
        let number = document.getElementById(id)
        let regExString = '/^\\d+$/'
        let errorMsg = 'Only Numbers are allowed'

        //checkMatch(number, regExString, errorMsg)
        if (!number.value.match(/^[0-9]+$/)) {
            number.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">Only Numbers are allowed</div>"
        } else {
            number.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }
    }

    function invalidEmail(id) {
        let email = document.getElementById(id)

        if (!email.value.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
            email.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">No valid email address</div>"
        } else {
            email.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }

    }

    function checkMatch(inputElement, regExString, errorMsg) {
        if (!inputElement.value.match(regExString)) {
            inputElement.style.borderColor = "red"
            document.getElementById('error').innerHTML = "<div class=\"alert alert-warning\">" + errorMsg + "</div>"
        } else {
            inputElement.style.borderColor = "#CED4DA"
            document.getElementById('error').innerHTML = ""
        }
    }
}