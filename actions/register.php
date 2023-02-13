<?php
include "../classes/User.php";

// create object 
$user = new User;

// $_POST holds the data from the form register.php in actions forlder
$user->store($_POST);