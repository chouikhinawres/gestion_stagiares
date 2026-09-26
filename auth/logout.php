<?php

session_start();        // démarrer la session

session_unset();        // supprimer toutes les variables session

session_destroy();      // détruire la session

header("Location: form_login.php"); // redirection vers login

exit();

?>