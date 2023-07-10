<?php
class Tickets
{
    public function __construct(private DatabaseTickets $database, private Security $sicher)
    {
    }
}
?>