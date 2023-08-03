<?php

class DatabaseTickets
{
    public function __construct(private Security $sicher, private PDO $dbwrite, private PDO $dbreade)
    { 
    }
}
?>