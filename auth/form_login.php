<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login UI</title>
    <link rel="stylesheet" href="styl.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
          rel="stylesheet">
</head>
<body>

<?php
/* -------------------------------------------------
   1️⃣  Affichage du message d’erreur (si présent)
   ------------------------------------------------- */
session_start();
if (!empty($_SESSION['login_error'])) {
    echo '<div class="login-error">' .
         htmlspecialchars($_SESSION['login_error']) .
         '</div>';
    // On enlève le message après l'avoir affiché pour qu'il ne reste pas en mémoire
    unset($_SESSION['login_error']);
}
?>

<div class="container">
    <div class="login-box">

        <!-- ===================================================== -->
        <!--   FORMULAIRE (POST vers login.php)                  -->
        <!-- ===================================================== -->
        <form class="left-side" action="login.php" method="POST" autocomplete="off">
            <h2>Login</h2>

            <div class="input-group">
                <input type="email"
                       name="email"
                       id="email"
                       required
                       autocomplete="username">
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <input type="password"
                       name="password"
                       id="password"
                       required
                       autocomplete="current-password">
                <label for="password">Password</label>
            </div>

            <button type="submit" class="login-btn">Login</button>

            <p class="signup-text">
                Don't have an account?
                <a href="inscri.html">Sign Up</a>
            </p>
        </form>

        <div class="right-side">
            <h1>WELCOME<br>BACK!</h1>
        </div>

    </div>
</div>

</body>
</html>
