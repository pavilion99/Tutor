<?php
define("HOST", "sql.spencercolton.tech");
define("USERNAME", "pavilion99");
define("PASSWORD", "\$fcrapr3qrfxgbc14");
define("DATABASE", "oh_tutoring");

function getSQL()
{
    return new mysqli(HOST, USERNAME, PASSWORD, DATABASE);
}