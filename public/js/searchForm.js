async function searchForUsers() {
    let email = document.getElementById('search_form_email').value
    let firstName = document.getElementById('search_form_first_name').value
    let givenName = document.getElementById('search_form_given_name').value
    let streetName = document.getElementById('search_form_street_name').value
    let city = document.getElementById('search_form_city').value
    let phoneNumber = document.getElementById('search_form_phone_number').value

    document.getElementById('table_container').innerHTML = ""
    let token = document.getElementById('token').dataset.token
    const paramBody = {
        email: email,
        first_name: firstName,
        given_name: givenName,
        street_name: streetName,
        city: city,
        phone_number: phoneNumber
    }
    const settings = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token,
        },
        body: JSON.stringify(paramBody)
    };
    try {
        const fetchResponse = await fetch(`http://pp2sf.local/api/search`, settings);
        let jsonRespons = await fetchResponse.json();
        let body = JSON.parse(jsonRespons);
        if (204 === fetchResponse.status) {
            document.getElementById('table_container').innerHTML = "<div class=\"alert alert-warning\">No match found</div>"
        } else {
            let table = "<table class=\"table table-bordered table-hover\">\n" +
                "           <th scope=\"col\" colspan=\"3\" style=\"text-align: center\">\n" +
                "               <div class=\"th-inner \">Search Result</div>\n" +
                "               <div class=\"fht-cell\"></div>\n" +
                "           </th>\n" +
                "            <tr>\n" +
                "               <th scope=\"col\" style=\"text-align: center\">E-Mail</th>\n" +
                "               <th scope=\"col\" style=\"text-align: center\">First Name</th>\n" +
                "               <th scope=\"col\" style=\"text-align: center\">Given Name</th>\n" +
                "            </tr>"

            for (let user of body) {
                table += `<tr>
                        <td><a href="details/${user["email"]}">${user["email"]}</a></td>
                        <td>${user["first_name"]}</td>
                        <td>${user["given_name"]}</td>
                      </tr>`
            }
            table += "</table>";
            document.getElementById('table_container').innerHTML = table;
        }

    } catch (e) {
        return e;
    }
}