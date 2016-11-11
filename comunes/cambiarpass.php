<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Cambiar contraseña</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    <body><?php
        require "auxiliar.php";

        if (!usuario_logueado()) {
            header("Location: /bd/");
        }

        menu(CTX_LOGIN);

        $pass_vieja = filter_input(INPUT_POST, "pass_a");
        $pass  = filter_input(INPUT_POST, "pass");
        $pass_confirmar = filter_input(INPUT_POST, "pass_c");

        try {
            $error = [];
            comprobar_existen([$pass_vieja, $pass, $pass_confirmar]);
            $pass_vieja = trim($pass_vieja);
            $pass_confirmar = trim($pass_confirmar);
            $pass = trim($pass);
            $pdo = conectar_bd();
            comprobar_credenciales($pdo, $_SESSION['login'], $pass_vieja, $error);
            comprobar_errores($error);
            modificar_password($pdo, $_SESSION['login'], $pass, $pass_confirmar, $error);
            comprobar_errores($error);
            header("Location: /bd/");
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">Cambiar contraseña</div>
                        <div class="panel-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="login">Contraseña actual *</label>
                                    <input type="password" id="pass_a" name="pass_a" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="pass">Contraseña nueva *</label>
                                    <input type="password" id="pass" name="pass" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="pass">Confirma contraseña nueva *</label>
                                    <input type="password" id="pass_c" name="pass_c" class="form-control" />
                                </div>
                                <button type="submit" class="btn btn-default">Confirmar</button>
                                <a href="/bd/" class="btn btn-warning" role="button">Cancelar</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
