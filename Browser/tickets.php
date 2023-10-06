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
        
        } else { echo '
        
            <div class="panel">

                <div class="panel-heading">
                    <h3 class="panel-title">50€ pro Ticket</h3>
                    <label for="ticket">Anzahl der Tickets: </label><input type="number" min="1" max="4" value="1" id="ticket">

                </div>
                <div class="panel-body">
                    <!-- Display status message -->
                    <div id="paymentResponse" class="hidden"></div>

                    <!-- Set up a container element for the button -->
                    <div id="paypal-button-container"></div>
                </div>
                <script>
                    paypal.Buttons({
                        // Sets up the transaction when a payment button is clicked
                        createOrder: async (data, actions) => {
                            return actions.order.create({

                                "intent": "CAPTURE",
                                "purchase_units": [{
                                    "reference_id": <?php echo $_COOKIE["UId"]; ?>,
                                    "description": "Ticket",
                                    "amount": {
                                        "currency_code": "EUR",
                                        "value": document.getElementById("ticket").value * 1.00,
                                        "breakdown": {
                                            "item_total": {
                                                "currency_code": "EUR",
                                                "value": document.getElementById("ticket").value * 1.00
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
                                            "return_url": "https://abi24bws.de/Tickets.html",
                                            "cancel_url": "https://abi24bws.de/homepage.html"
                                        }
                                    }
                                }
                            });
                        },
                        // Finalize the transaction after payer approval
                        onApprove: (data, actions) => {
                            return actions.order.capture().then(function(orderData) {
                                setProcessing(true);

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
                                                messageText.textContent = "";
                                            }, 5000);
                                        }
                                        setProcessing(false);
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

                    // Show a loader on payment form processing
                    const setProcessing = (isProcessing) => {
                        if (isProcessing) {
                            document.querySelector(".overlay").classList.remove("hidden");
                        } else {
                            document.querySelector(".overlay").classList.add("hidden");
                        }
                    }
                </script>
            </div>';}?>

    </section>
    <script>
        const wo = "tickets";
    </script>
</body>

</html>