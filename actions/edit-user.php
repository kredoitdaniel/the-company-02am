<?php

    include "../classes/User.php";

    $user = new User;

    $user->update($_POST, $_FILES);
    // $_POST ~~ holds an array of the form-data
    // $_FILES ~~ holds an array of the items/files uploaded to the POST Method
?>