<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<<<<<<< bd6fda6af1eab21e97a2125b2f969563fb209235
        <title>Insertar un nuevo departamento</title>
=======
        <title>Insertar un departamento</title>
>>>>>>> se borran departamentos
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <style type="text/css">
            body { padding: 60px; }
        </style>
    </head>
<<<<<<< bd6fda6af1eab21e97a2125b2f969563fb209235
    <body><?php
        require "auxiliar.php";

        $dept_no = filter_input(INPUT_POST, "dept_no");
        $dnombre = filter_input(INPUT_POST, "dnombre");
        $loc     = filter_input(INPUT_POST, "loc");

        try {
            $error = [];
            comprobar_existen([$dept_no, $dnombre, $loc]);
            comprobar_dept_no($dept_no, $error, ESC_INSERTAR);
            comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
            comprobar_loc($loc, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $orden = $pdo->prepare("insert into depart (dept_no, dnombre, loc)
                                    values (:dept_no, :dnombre, :loc)");
            $orden->execute([
                ':dept_no' => $dept_no,
                ':dnombre' => $dnombre,
                ':loc'     => $loc
            ]);
            header("Location: bd.php");
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>
=======
    <body>
>>>>>>> se borran departamentos
        <div class="container">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">Insertar un departamento</div>
                        <div class="panel-body">
                            <form action="" method="post">
                                <div class="form-group">
<<<<<<< bd6fda6af1eab21e97a2125b2f969563fb209235
                                    <label for="dept_no">Número *</label>
                                    <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="dnombre">Nombre *</label>
=======
                                    <label for="dept_no">Número</label>
                                    <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="dnombre">Nombre</label>
>>>>>>> se borran departamentos
                                    <input type="text" id="dnombre" name="dnombre"  value="<?= htmlentities($dnombre) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="loc">Localidad</label>
                                    <input type="text" id="loc" name="loc"  value="<?= htmlentities($loc) ?>" class="form-control" />
                                </div>
                                <button type="submit" class="btn btn-default">Insertar</button>
                                <button type="reset" class="btn">Limpiar</button>
                                <a href="bd.php" class="btn btn-warning" role="button">Cancelar</a>
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
