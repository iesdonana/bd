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
        require 'auxiliar.php';

        comprobar_logueado();
        menu();

        $pass = filter_input(INPUT_POST, "pass_antigua");
        $pass_nueva1 = filter_input(INPUT_POST, "pass_nueva");
        $pass_nueva2 = filter_input(INPUT_POST, "pass_nueva_2");
        try {
            $error = [];
            comprobar_existen([$pass, $pass_nueva1, $pass_nueva2]);
            $pdo = conectar_bd();
            comprobar_password($pdo, $pass, $error);
            comprobar_password_nuevas($pass_nueva1, $pass_nueva2, $error);
            comprobar_errores($error);
            cambiar_password($pdo, $pass_nueva1);
            header("Location: /bd/comunes/logout.php");
        } catch (Exception $e) {
            mostrar_errores($error);
        }?>
        <div class="container">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">Cambio de contraseña</div>
                        <div class="panel-body">
                            <form action="" method="post">
                                <label for="pass_antigua">Contraseña antigua:</label>
                                <input type="password" id="pass_antigua" name="pass_antigua" value="" class="form-control" /><br/>
                                <label for="pass_nueva">Contraseña nueva:</label>
                                <input type="password" id="pass_nueva" name="pass_nueva" value="" class="form-control"/><br/>
                                <label for="pass_nueva_2">Repite contraseña nueva:</label><br/>
                                <input type="password" id="pass_nueva_2" name="pass_nueva_2" value="" class="form-control"/><br/>
                                <input type="submit" value="Confirmar"  class="btn btn-default"/>
                                <a href="index.php" class="btn btn-warning" role="button">Cancelar</a>
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
