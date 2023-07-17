
async function dieFuenfPersoenlicheDatenAnRegister(vorname,nachname,klasse,email,passwort)
{
  console.log("Werte weiterleitenfunktion erfolgt");
  const response = await fetch("https://abi24bws.de/Register", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"vorname":vorname,"nachname":nachname,"klasse":klasse,"email":email,"passwort":passwort}),
    });
  const result = response.json();
  console.log(result);
  document.getElementById('textfeld').innerHTML = "Sie m&uumlssen Ihre Registrierungen, mit der an Ihr gesendete E-Mail, best&aumltigen.";
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
      document.getElementById('infotext').innerHTML = "Pass&oumlrter &uumlbereinstimmen nicht";
    }
  }
  else{
    document.getElementById('infotext').innerHTML = "E-Mails &uumlbereinstimmen nicht";
  }
}

async function loginanfrage()
{
  const emailvar = document.getElementById('e-mail').value;
  const passwortvar = document.getElementById('passwort').value;
  console.log(emailvar+passwortvar);
  const response = await fetch("https://abi24bws.de/Login", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    });
  const result = response.json();
  console.log({result});
  document.getElementById('textfeld').innerHTML = "Sie wurden angemeldet. Oder auch nicht."
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
  }); 
const result = await response.json();
document.getElementById('textfeld').innerHTML = "In kürze erhalten Sie die E-Mail.";
}

async function Passwortzurucksetzen()
{
  const emailvar = window.location.href.slice(43);
  const passwortvar = document.getElementById('passwort').value;

  const response = await fetch("https://abi24bws.de/Reseting", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"email":emailvar,"passwort":passwortvar}),
    });
  const result = response.json();
  console.log({result});
  document.getElementById('textfeld').innerHTML = "Ihr Passwort wurde zur&uumlckgesetzt und k&oumlnnen sich unter Login wieder anmelden.";
}