<?php
class Tickets
{
    public function __construct(private DatabaseUsers $database, private Security $sicher)
    {
    }
}
?>