async function Email_Bestaetigen() {
    const id = window.location.href.slice(41);

    const response = await fetch("https://abi24bws.de/bestaetigung", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "text/html,application/js"
        },
        body: JSON.stringify({ "id": id })
    }).then((response) => response.json())
        .then((data) => {
            return data;
        });

    if(response.Status=="ERROR"){            
        document.getElementById('textfeld').style.visibility = "visible";
        document.getElementById('textfeld').innerHTML = response.Message;
    }

    else{
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').style.backgroundColor = "green";
      document.getElementById('textfeld').style.borderColor = "green";
      document.getElementById('textfeld').innerHTML = "Die Email Addresse wurde erfolgreich best&auml;tigt.";
    }
}

async function Identitaet_bestaetigt(registrierungs_id) {
    const response = await fetch("https://abi24bws.de/Freigeschaltet", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ "registrierungs_id": registrierungs_id })
    }).then((response) => response.json())
    .then((data) => {
        return data;
    });
}