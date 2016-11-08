<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
    </head>
    <body><?php
        require "auxiliar.php";

        menu();

        $login = filter_input(INPUT_POST,$login);
        $pass = filter_input(INPUT_POST,$pass);
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
