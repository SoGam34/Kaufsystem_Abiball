
async function dieFuenfPersoenlicheDatenAnRegister(vorname,nachname,klasse,email,passwort)
{
  console.log("Werte weiterleitenfunktion erfolgt");
  const response = await fetch("https://abi24bws.de/Register", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"vorname":vorname,"nachname":nachname,"klasse":klasse,"email":email,"passwort":passwort}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
  console.log(response.Status);
  console.log(response.Message);
  document.getElementById('textfeld').style.visibility = "visible"
  document.getElementById('textfeld').innerHTML = response.Message;
}
async function UeberpruefenPasswortUndEmailBestaetigen()
{
  const vornamevar = document.getElementById('UserVorname').value;
  const nachnamevar = document.getElementById('UserNachname').value;
  const klassevar = document.getElementById('klasse').value;
  const emailvar = document.getElementById('e-mail').value;
  const emailpruefenvar = document.getElementById('e-mailpruefen').value;
  const passwortvar = document.getElementById('passwort').value;
  const passwortpruefenvar = document.getElementById('passwortpruefen').value;
  console.log(vornamevar+nachnamevar+klassevar+emailvar+emailpruefenvar+passwortvar+passwortpruefenvar);
  if(emailvar==emailpruefenvar){
    console.log("Emailpruefen erfolgt");
    if(passwortvar==passwortpruefenvar){
      console.log("Passwort pruefen erfolgt")
      dieFuenfPersoenlicheDatenAnRegister(vornamevar,nachnamevar,klassevar,emailvar,passwortvar);
    }
    else{
      document.getElementById('textfeld').style.visibility = "visible"
      document.getElementById('textfeld').innerHTML = "Pass&oumlrter &uumlbereinstimmen nicht";
    }
  }
  else{
    document.getElementById('textfeld').style.visibility = "visible"
    document.getElementById('textfeld').innerHTML = "E-Mails &uumlbereinstimmen nicht";
  }
}

async function loginanfrage()
{
  const emailvar = document.getElementById('e-mail').value;
  const passwortvar = document.getElementById('passwort').value;
  const response = await fetch("https://abi24bws.de/Login", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
    console.log(response);
    if (response.Erfolgreich==false){
      document.getElementById('textfeld').style.visibility = "visible"
      document.getElementById('textfeld').innerHTML = "Account existiert nicht";
    }
    else{
      document.getElementById('textfeld').style.visibility = "visible"
      document.getElementById('textfeld').innerHTML = response.Message;
    }
}

async function emailfuerzuruck()
{
  const emailvar = document.getElementById('e-mail').value;
  console.log(emailvar);
  const response = await fetch("https://abi24bws.de/RequestEmail", {
    method: "POST", // or 'PUT'
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({email:emailvar}),
  }).then((response) => response.json())
  .then((data) => {
    return data;
  });
  console.log(response);
  if (response.Status=="OK"){
    document.getElementById('textfeld').innerHTML = "In k&uumlrze erhalten Sie die E-Mail.";
  }
  else {
    document.getElementById('infofeld').style.visibility = "visible"
    document.getElementById('infofeld').innerHTML = response.Message;
  }
}

async function Passwortzurucksetzen()
{
  const emailvar = window.location.href.slice(43);
  const passwortvar = document.getElementById('passwort').value;
  const passwortuberprufen = document.getElementById('passwortuberprufen').value;
  if (passwortvar==passwortuberprufen) {
    const response = await fetch("https://abi24bws.de/Reseting", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
    console.log(response);
    if(response.Status=="ERROR"){
      document.getElementById('infofeld').style.visibility = "visible"
      document.getElementById('infofeld').innerHTML = response.Message;
    }
    if(response.Status=="OK"&&response.Erfolgreich==false){
      document.getElementById('textfeld').style.visibility = "visible"
      document.getElementById('textfeld').innerHTML = "Ihr Passwort wurde zur&uumlckgesetzt und k&oumlnnen sich unter Login wieder anmelden?";
    }
    if(response.Status=="OK"&&response.Erfolgreich==true){
      document.getElementById('textfeld').style.visibility = "visible"
      document.getElementById('textfeld').innerHTML = "Ihr Passwort wurde zur&uumlckgesetzt und k&oumlnnen sich unter Login wieder anmelden.";
    }
  }
  else {
    document.getElementById('infofeld').style.visibility = "visible"
    document.getElementById('infofeld').innerHTML = "Passw&oumlrter &uumlbereinstimmen nicht";
  }
}