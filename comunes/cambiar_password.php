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
    <body>
        <?php
        require "auxiliar.php";
        if (!usuario_logueado()) {
            header("Location: /iesdonana/bd/");
        }
        menu(CTX_USUARIO); ?>

        <div class="container"><?php
            $vieja         = filter_input(INPUT_POST, "vieja");
            $nueva         = filter_input(INPUT_POST, "nueva");
            $nueva_confirm = filter_input(INPUT_POST, "nueva_confirm");
            try {
                $error = [];
                comprobar_existen([$vieja, $nueva, $nueva_confirm]);
                $pdo = conectar_bd();
                comprobar_vieja($pdo, $vieja, $error);
                comprobar_nueva($nueva, $nueva_confirm, $error);
                comprobar_errores($error);
                $orden = $pdo->prepare("update usuarios
                                           set pass = :nueva
                                         where nombre = :login");
                $orden->execute([
                    ':login' => $_SESSION['login'],
                    ':nueva' => password_hash($nueva, PASSWORD_DEFAULT)
                ]);
                header("Location: /iesdonana/bd/");
            } catch (Exception $e) {
                mostrar_errores($error);
            } ?>

            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">Cambiar contraseña</div>
                        <div class="panel-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="vieja">Contraseña anterior *</label>
                                    <input type="password" id="vieja" name="vieja" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="nueva">Nueva contraseña *</label>
                                    <input type="password" id="nueva" name="nueva" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="nueva_confirm">Confirmar nueva contraseña *</label>
                                    <input type="password" id="nueva_confirm" name="nueva_confirm" class="form-control" />
                                </div>
                                <button type="submit" class="btn btn-default">Aceptar</button>
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
