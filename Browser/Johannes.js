async function jstest() {
    const id = window.location.href.slice(41);
    const response = await fetch("https://abi24bws.de/bestaetigung", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ "id": id })
    }).then((response) => response.json())
        .then((data) => {
            console.log(data);
        });
}

async function Identitaet_bestaetigt(registrierungs_id) {
    const response = await fetch("https://abi24bws.de/Freigeschaltet", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ "registrierungs_id": registrierungs_id })
    });

    window.location.reload();
}