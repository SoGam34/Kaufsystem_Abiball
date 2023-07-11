
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
  const result = await response.json();
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

    }
  }
  else{

  }
}
async function emailfuerzuruck()
{
  const emailvar = document.getElementById('e-mail').value;
  console.log(emailvar);
  const response = await fetch("https://abi24bws.de/Register", {
    method: "POST", // or 'PUT'
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({email:emailvar}),
  }); 
const result = await response.json();
}