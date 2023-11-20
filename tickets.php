<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta NAME="Author" CONTENT="Johannes Hoffmann">
    <title>Tickets kaufen</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="bws.png">
    <script src='https://www.paypal.com/sdk/js?client-id=AWGRHS9BmRSS27GZY1mwIywoVoKeC3XSmhCyYtaL1VTHf0SUItDcIXdAj281tsrbr5Tlg0wiznVi9UgS&currency=EUR'></script>
    <script>
        function TicketAnzahl() {
            document.getElementById("PAY").style.visibility = "visible";
            document.getElementById("auswahl").style.visibility = "hidden";
        }
    </script>
</head>

<body>
    <div class="navbar">
        <div class="navbarunterLoginRegister" id="navbarLRAlternative">
            <a href="login.html">Login</a>
            <a href="registrieren.html">Registrieren</a>
        </div>
        <div class="navbarunterHomepage">
            <a href="bilder.html">Bilder</a>
            <a href="homepage.html">Homepage</a>
            <a href="tickets.html">Tickets</a>
            <a href="program.html">Programm</a>
        </div>
        <a href="support.html">Support</a>
    </div>
    <h1 style="text-align:center;margin-top: 55px;position: relative">Tickets</h1>
    <section class="loginregisterstyle">
        <?php
        if (!isset($_COOKIE["UId"])) {

            echo ' <div>
                Sie k&oumlnnen diese Seite nur ansehen, wenn Sie regestriert und angemeldet sind.<br>
                <a href="login.html">Hier zum Login</a>

            </div>';
        } else {
            echo '

            <div id="auswahl">
                <h3 >50€ pro Ticket</h3>
                <label for="ticket">Ticket anzahl auswaehlen:</label></td>
				<select name="ticket" id="ticket">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
				</select>
                <button onclick="TicketAnzahl()">Besteatigen</button>
            </div>
        
            
            <div class="panel" id="PAY" style="visibility: hidden;">

                <div class="panel-heading">
                    

                </div>
                <div class="panel-body">
                    <!-- Display status message -->
                    <div id="paymentResponse" class="hidden"></div>

                    <!-- Set up a container element for the button -->
                    <div id="paypal-button-container" ></div>
                </div>
                <script>
                    paypal.Buttons({
                        // Sets up the transaction when a payment button is clicked
                        createOrder: async (data, actions) => {
                            return actions.order.create({

                                "intent": "CAPTURE",
                                "purchase_units": [{
                                    "reference_id": ' . '"' . $_COOKIE["UId"] . '"' . ',
                                    "description": "Ticket",
                                    "amount": {
                                        "currency_code": "EUR",
                                        "value": document.getElementById("ticket").value * 50.00,
                                        "breakdown": {
                                            "item_total": {
                                                "currency_code": "EUR",
                                                "value": document.getElementById("ticket").value * 50.00
                                            }
                                        }
                                    }
                                }],
                                "payment_source": {
                                    "paypal": {
                                        "experience_context": {
                                            "payment_method_preference": "IMMEDIATE_PAYMENT_REQUIRED",
                                            "brand_name": "abi24bws.de",
                                            "locale": "de-DE",
                                            "landing_page": "LOGIN",
                                            "user_action": "PAY_NOW",
                                            "return_url": "https://abi24bws.de/KaufERFOLGREICH.html",
                                            "cancel_url": "https://abi24bws.de/KaufFEHLER.html"
                                        }
                                    }
                                }
                            });
                        },
                        // Finalize the transaction after payer approval
                        onApprove: (data, actions) => {
                            return actions.order.capture().then(function(orderData) {
                                
                                var postData = {
                                    paypal_order_check: 1,
                                    order_id: orderData.id,
                                    amount: document.getElementById("ticket").value
                                };
                                fetch("KaufTicket", {
                                        method: "POST",
                                        headers: {
                                            "Accept": "application/json"
                                        },
                                        body: encodeFormData(postData)
                                    })
                                    .then((response) => response.json())
                                    .then((result) => {
                                        if (result.status == 1) {
                                            const messageContainer = document.querySelector("#paymentResponse");
                                            messageContainer.classList.remove("hidden");
                                            messageContainer.textContent = result.msg;

                                        } else {
                                            const messageContainer = document.querySelector("#paymentResponse");
                                            messageContainer.classList.remove("hidden");
                                            messageContainer.textContent = result.msg;

                                            setTimeout(function() {
                                                messageContainer.classList.add("hidden");
                                            }, 5000);
                                        }
                                    })
                                    .catch(error => console.log(error));
                            });
                        }
                    }).render("#paypal-button-container");

                    const encodeFormData = (data) => {
                        var form_data = new FormData();

                        for (var key in data) {
                            form_data.append(key, data[key]);
                        }
                        return form_data;
                    }
                </script>
            </div>';
        } ?>

    </section>
    <script>
        const wo = "tickets";
    </script>
</body>

</html>