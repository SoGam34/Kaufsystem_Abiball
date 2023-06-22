asynch function dieFuenfPersoenlicheDatenAnRegister(vorname,nachname,klasse,email)
{
  const response = await fetch("https://abi24bws.de/Register", {
      method: "POST", // or 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({"vorname":vorname,"nachname":nachname,"klasse":klasse,"email":email}),
    });
  const result = await response.json();
}
asynch function UeberpruefenPasswortUndEmailBestaetigen(email,emailu,password,passwordu)
{
  
}
