
async function dieFuenfPersoenlicheDatenAnRegister(vorname, nachname, klasse, email, passwort)
{
  if (localStorage.getItem("cookieszulassenabi24bws")=="true" || localStorage.getItem("cookieszulassenabi24bws") == null) {
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

    if (localStorage.getItem("cookieszulassenabi24bws")=="false") {
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').style.backgroundColor = "orange";
      document.getElementById('textfeld').style.borderColor = "red";
      document.getElementById('textfeld').innerHTML = "Sie m&uumlssen unsere Cookies akzeptieren, um ein Account erstellen zu können und zu verwenden.";
  }
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
      document.getElementById('textfeld').innerHTML = "Beide eingegebenen Passw&oumlrter &uumlbereinstimmen nicht";
    }
  }
  else{
    document.getElementById('textfeld').style.visibility = "visible";
    document.getElementById('textfeld').innerHTML = "Beide eingegebenen E-Mails &uumlbereinstimmen nicht";
  }
}

async function loginanfrage()
{
  const emailvar = document.getElementById('e-mail').value;
  const passwortvar = document.getElementById('passwort').value;
  if (localStorage.getItem("cookieszulassenabi24bws")=="true" || localStorage.getItem("cookieszulassenabi24bws") == null) {
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
      document.getElementById('textfeld').innerHTML = "Die Anmeldung ist fehlgeschlagen, bitte &uuml;berpr&uuml;fen Sie ihre Email oder Passwort";
    }
    else{
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').style.backgroundColor = "green";
      document.getElementById('textfeld').style.borderColor = "green";
      document.getElementById('textfeld').innerHTML = "Sie sind jetzt Angemeldet";
      document.getElementById('navbarLRAlternative').innerHTML = '<button type="button" id="logoutButtonid" class="logoutButton" onclick="Ausloggen()">Logout</button>';
    }

    if (localStorage.getItem("cookieszulassenabi24bws")=="false") {
      document.getElementById('textfeld').style.visibility = "visible";
      document.getElementById('textfeld').style.backgroundColor = "orange";
      document.getElementById('textfeld').style.borderColor = "red";
      document.getElementById('textfeld').innerHTML = "Sie m&uumlssen unsere Cookies akzeptieren, um ein Account erstellen zu können und zu verwenden";
  }
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

async function cookiepopupakzeptieren() {
  localStorage.setItem("cookieszulassenabi24bws", "true");
  window.location.reload();
}

async function cookiepopupablehnen() {
  localStorage.setItem("cookieszulassenabi24bws", "false");
  window.location.reload();
}

async function cookieeinstellungreseten() {
  localStorage.removeItem("cookieszulassenabi24bws");
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
      if (document.getElementById('loginregisterstyleid') == true) {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstyledark';
      }
      if (document.getElementById('textfeld') == true) {
        document.getElementById('textfeld').className = 'loginregisterstyledark';
      }
      if (document.getElementById('registrierenButtonid') == true) {
        document.getElementById('registrierenButtonid').className = 'registrierenButtondark';
      }
    break;
    case "creamy":
      document.getElementById('torso').className = 'bodydesigncreamy';
      document.getElementById('navbarid').className = 'navbarcreamy';
      document.getElementById('navbarLRAlternative').className = 'navbarunterLoginRegistercreamy';
      document.getElementById('navbarunterHomepageid').className = 'navbarunterHomepagecreamy';
      if (document.getElementById('loginregisterstyleid') == true) {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstylecreamy';
      }
      if (document.getElementById('textfeld') == true) {
        document.getElementById('textfeld').className = 'loginregisterstylecreamy';
      }
      if (document.getElementById('registrierenButtonid') == true) {
        document.getElementById('registrierenButtonid').className = 'registrierenButtoncreamy';
      }
    break;
    default:
      document.getElementById('torso').className = 'bodydesign';
      document.getElementById('navbarid').className = 'navbar';
      document.getElementById('navbarLRAlternative').className = 'navbarunterLoginRegister';
      document.getElementById('navbarunterHomepageid').className = 'navbarunterHomepage';
      if (document.getElementById('loginregisterstyleid') == true) {
        document.getElementById('loginregisterstyleid').className = 'loginregisterstyle';
      }
      if (document.getElementById('textfeld') == true) {
        document.getElementById('textfeld').className = 'loginregisterstyle';
      }
      if (document.getElementById('registrierenButtonid') == true) {
        document.getElementById('registrierenButtonid').className = 'registrierenButton';
      }
    break;
  }
  switch (localStorage.getItem("cookieszulassenabi24bws")) {
    case null:
      document.getElementById('popupfenster').style.visibility = "visible";
      document.getElementById('popupfenster').innerHTML = '<font color="black"><font size="5"><B>Cookie-Einverständniserklärung</B></font><br /><br /><br />Diese Website verwendet Cookies, um Ihnen ein optimales Online-Erlebnis zu bieten. <br />Indem Sie die Seite nutzen, erklären Sie sich mit der Verwendung von notwendigen, <br />analytischen und funktionalen Cookies einverstanden. Sie können Ihre Einwilligung <br />jederzeit widerrufen, indem Sie die Einstellungen Ihres Browsers anpassen. Beachten <br />Sie, dass das Deaktivieren von Cookies die Funktionalität der Website <br />beeinträchtigen kann.<br /><br />Kontakt:<br />Für Fragen erreichen Sie uns unter <a href="mailto:support@abi24bws.de">support@abi24bws.de</a>.<br /></font><button type="button" id="registrierenButtonid" class="registrierenButton" onclick="cookiepopupakzeptieren()">Cookies akzeptieren</button><br><button type="button" id="registrierenButtonid" class="registrierenButton" onclick="cookiepopupablehnen()">Cookies ablehnen</button><br>';
      break;
    case "false":
      document.getElementById('popupfenster').style.display = "none";
      break;
    case "true":
      document.getElementById('popupfenster').style.display = "none";
      break;
    default:
      localStorage.setItem("cookieszulassenabi24bws", "false");
      break;
  }
  console.log("cookie = " + document.cookie);
  if (document.cookie.length>3) {
    var hatcookie = true;
  }
  else {
    var hatcookie = false;
  }
  console.log("Cookie erlaubt? " + localStorage.getItem("cookieszulassenabi24bws"));
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
      case "einstellungen":
        document.getElementById('datenloeschenid').innerHTML = '<button type="button" id="datenloeschenbutton" class="registrierenButton" onclick="datenloeschen()">pers&oumlnliche Daten l&oumleschen</button>'
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
      case "einstellungen":
        document.getElementById('datenloeschenid').innerHTML = 'Sie m&uumlssen angemldet sein, damit Sie verifiziert Daten l&oumlschen k&oumlnnen'
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