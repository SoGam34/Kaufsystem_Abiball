
async function dieFuenfPersoenlicheDatenAnRegister(vorname, nachname, klasse, email, passwort)
{
  const response = await fetch("https://abi24bws.de/Register", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
        "Accept": "text/html,application/js"
      },
      body: JSON.stringify({"vorname":vorname,"nachname":nachname,"klasse":klasse,"email":email,"passwort":passwort}),
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
      document.getElementById('textfeld').innerHTML = "Sie erhalten in den n&auml;chsten Minuten eine Best&auml;tingsemail an die angegebene Adresse.";
    }
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

  if(emailvar==emailpruefenvar){
    if(passwortvar==passwortpruefenvar){
      dieFuenfPersoenlicheDatenAnRegister(vornamevar,nachnamevar,klassevar,emailvar,passwortvar);
    }
    else{
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').innerHTML = "Passw&oumlrter &uumlbereinstimmen nicht";
    }
  }
  else{
    document.getElementById('textfeld').style.visibility = "visible";
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
        "Accept": "text/html,application/js"
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
    
    if(response.Status=="ERROR"){            
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').innerHTML = response.Message;
    }
    else if (response.Erfolgreich==false){
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').innerHTML = "Anmeldung fehlgeschlagen, bitte &uuml;berpr&uuml;fen Sie ihre Email oder Passwort";
    }
    else{
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').style.backgroundColor = "green";
      document.getElementById('textfeld').style.borderColor = "green";
      document.getElementById('textfeld').innerHTML = "Sie sind Angemeldet";
      document.getElementById('navbarLRAlternative').innerHTML = '<button type="button" id="logoutButtonid" class="logoutButton" onclick="Ausloggen()">Logout</button>';
    }
}

async function emailfuerzuruck()
{
  const emailvar = document.getElementById('e-mail').value;
  
  const response = await fetch("https://abi24bws.de/RequestEmail", {
    method: "POST", // or 'PUT'
    headers: {
      "Content-Type": "application/json",
      "Accept": "text/html,application/js"
    },
    body: JSON.stringify({email:emailvar}),
  }).then((response) => response.json())
  .then((data) => {
    return data;
  });
  
  if (response.Status=="OK"){
    document.getElementById('infofeld').style.visibility = "visible";
    document.getElementById('infofeld').style.backgroundColor = "green";
    document.getElementById('textfeld').style.borderColor = "green";
    document.getElementById('infofeld').innerHTML = "In k&uumlrze erhalten Sie die E-Mail zum zur&uuml;cksetzten ihres Passworts.";
  }
  else {
    document.getElementById('infofeld').style.visibility = "visible";
    document.getElementById('infofeld').innerHTML = response.Message;
  }
}

async function Passwortzurucksetzen()
{
  const emailvar = window.location.href.slice(28);
  const passwortvar = document.getElementById('passwort').value;
  const passwortuberprufen = document.getElementById('passwortuberprufen').value;

  if (passwortvar==passwortuberprufen) {
    const response = await fetch("https://abi24bws.de/Reseting", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
        "Accept": "text/html,application/js"
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
    
    if(response.Status=="ERROR"){
      document.getElementById('infofeld').style.visibility = "visible";
      document.getElementById('infofeld').innerHTML = response.Message;
    }
    else if(response.Erfolgreich==false){
      document.getElementById('infofeld').style.visibility = "visible";
      document.getElementById('infofeld').innerHTML = "Ihr Passwort konnte nicht zur&uuml;ck gesetzt werden";
    }
    else{
      document.getElementById('infofeld').style.visibility = "visible";
      document.getElementById('infofeld').style.backgroundColor = "green";
      document.getElementById('textfeld').style.borderColor = "green";
      document.getElementById('infofeld').innerHTML = "Ihr Passwort wurde zur&uumlckgesetzt und k&oumlnnen sich unter Login wieder anmelden.";
    }
  }
  else {
    document.getElementById('infofeld').style.visibility = "visible";
    document.getElementById('infofeld').innerHTML = "Passw&oumlrter &uumlbereinstimmen nicht";
  }
}

async function designwechsler()
{
  switch (localStorage.getItem("designmode")) {
    case null:
      localStorage.setItem("designmode", "dark");
    break;
    case "dark":
      localStorage.setItem("designmode", "creamy");
    break;
    case "creamy":
      localStorage.setItem("designmode", "light");
    break;
    case "light":
      localStorage.setItem("designmode", "dark");
    break;
    default:
      break;
  }
  window.location.reload();
}

async function cookieverarbeiter()
{
  switch (localStorage.getItem("designmode")) {
    case "dark":
      document.getElementById('torso').className = 'bodydesigndark';
      document.getElementById('navbarid').className = 'navbardark';
      document.getElementById('navbarLRAlternative').className = 'navbarunterLoginRegisterdark';
      document.getElementById('navbarunterHomepageid').className = 'navbarunterHomepagedark';
      try {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstyledark';
      } catch (nichts) {
        document.getElementById('textfeld').className = 'loginregisterstyledark';
      }
      document.getElementById('registrierenButtonid').className = 'registrierenButtondark';
    break;
    case "creamy":
      document.getElementById('torso').className = 'bodydesigncreamy';
      document.getElementById('navbarid').className = 'navbarcreamy';
      document.getElementById('navbarLRAlternative').className = 'navbarunterLoginRegistercreamy';
      document.getElementById('navbarunterHomepageid').className = 'navbarunterHomepagecreamy';
      try {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstylecreamy';
      } catch (nichts) {
        document.getElementById('textfeld').className = 'loginregisterstylecreamy';
      }
      document.getElementById('registrierenButtonid').className = 'registrierenButtoncreamy';
    break;
    default:
      document.getElementById('torso').className = 'bodydesign';
      document.getElementById('navbarid').className = 'navbar';
      document.getElementById('navbarLRAlternative').className = 'navbarunterLoginRegister';
      document.getElementById('navbarunterHomepageid').className = 'navbarunterHomepage';
      try {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstyle';
      } catch (nichts) {
        document.getElementById('textfeld').className = 'loginregisterstyle';
      }
      document.getElementById('registrierenButtonid').className = 'registrierenButton';
    break;
  }
  console.log(document.cookie);
  if (document.cookie.length>3) {
    var hatcookie = true;
  }
  else {
    var hatcookie = false;S
  }
  console.log("Hat Cookie? " + hatcookie);
  console.log(wo);
  if (hatcookie == true){
    document.getElementById('navbarLRAlternative').innerHTML = '<button type="button" id="logoutButtonid" class="logoutButton" onclick="Ausloggen()">Logout</button>';
    switch (wo) {
      case "abstimmung":
        document.getElementById('abstimmungNichtangemeldetNachricht').style.display = 'none';
        document.getElementById('abstimmungAuswahl').style.visibility = "visible";
        document.getElementById('abstimmungButton').style.visibility = "visible";
        break;
      case "bilder":
        document.getElementById('bilderNichtangemeldetNachricht').style.display = 'none';
        document.getElementById('inhaltBilder').style.visibility = "visible";
      break;
      case "tickets":
        document.getElementById('ticketsNichtangemeldetNachricht').style.display = 'none';
        document.getElementById('inhaltTickets').style.visibility = "visible";
      break;
      default:
        console.log("Wie?");
      break;
    }
        
  }
  else {
    switch (wo) {
      case "abstimmung":
        document.getElementById('abstimmungNichtangemeldetNachricht').style.visibility = 'visible';
        document.getElementById('abstimmungAuswahl').style.display = "none";
        document.getElementById('abstimmungButton').style.display = "none";  
      break;
      case "bilder":
        document.getElementById('bilderNichtangemeldetNachricht').style.visibility = 'visible';
        document.getElementById('inhaltBilder').style.display = "none";
      break;
      case "tickets":
        document.getElementById('ticketsNichtangemeldetNachricht').style.visibility = 'visible';
        document.getElementById('inhaltTickets').style.display = "none";
      break;
      default:
        console.log("Wie?");
      break;
    }
  }
}

async function Ausloggen()
{
  const response = await fetch("https://abi24bws.de/Logout", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
        "Accept": "text/html,application/js"
      },
    }).then((response) => response.json())
    .then((data) => {
      return data;
    });
    window.location.reload();
}

async function abiballAbstimmung()
{
  const abiballOrt = document.getElementById('ortAbstimmung').value;
  console.log(abiballOrt);
/*   const response = await fetch("https://abi24bws.de/Register", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
        "Accept": "text/html,application/js"
      },
      body: JSON.stringify({"vorname":vorname,"nachname":nachname,"klasse":klasse,"email":email,"passwort":passwort}),
    }).then((response) => response.json())
    .then((data) => {
      return data;
    }); */
    window.location.reload();
}