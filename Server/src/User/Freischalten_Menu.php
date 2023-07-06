<!DOCTYPE html>
<HTML>

<HEAD>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html CHARSET=iso-8859-1">
    <META NAME="Author" CONTENT="Johannes Hoffmann">
    <META NAME="Email-Bestaetigung" CONTENT="Mozilla/4.05 [de] (WinNT; I) [Netscape]">
    <TITLE>Email-Bestaetigung</TITLE>

    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="bws.png">
    <style>
        table
        {
            border: 5px;
            border-color: red;
        }
    </style>
    <script type="text/javascript" src="Browser/Johannes.js"></script>
</HEAD>

<BODY>
    <div class="navbar">
        <a href="login.html">Login</a>
        <a href="registrieren.html" ; style="margin-left: 17%;">Registrieren</a>
        <a href="bilder.html" ; style="margin-left: 8%;">Bilder</a>
        <a href="tickets.html" ; style="margin-left: 8%;">Tickets</a>
        <a href="program.html" ; style="margin-left: 8%;">Program</a>
        <a href="homepage.html" ; style="margin-left: 8%;">Homepage</a>
        <a href="support.html" ; style="margin-left: 0%; margin-right: 11%;">Support</a>
    </div>

    <div  class="loginregisterstyle" style="border: 5px; border-color:blue">
    <?php 
    include "DatabaseUsers.php";
    $dbUSER= new DatabaseUsers;

    $dbUSER->FreischaltungsUebersicht();
    ?>
    </div>
</BODY>

</HTML>