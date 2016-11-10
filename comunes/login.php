<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
    </head>
    <body><?php
        require "auxiliar.php";

        if (usuario_logueado()) {
            header("Location: /bd/");
        }
        menu();

        $login = filter_input(INPUT_POST,"login");
        $pass = filter_input(INPUT_POST,"pass");

        try {
            $error = [];
            comprobar_existen([$login, $pass]);
            $login = trim($login);
            $pass = trim($pass);
            $pdo = conectar_bd();
            comprobar_credenciales($pdo, $login, $pass, $error);
            comprobar_errores($error);
            $_SESSION['login'] = $login;
            header("Location: /bd/");
        } catch (Exception $e) {
            mostrar_errores($error);
        }

        ?>

        <form action="" method="post">
            <fieldset>
                <legend>Login</legend>
                <label for="login">Usuario *:</label>
                <input type="text" id="login" name="login" value="<?= htmlentities($login) ?>" /><br />
                <label for="pass">Contraseña *:</label>
                <input type="password" id="pass" name="pass" /><br />
                <input type="submit" name="" value="Login" />
            </fieldset>
        </form>
    </body>
</html>
