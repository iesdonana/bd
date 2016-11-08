<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar un departamento</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    <body><?php
        require "auxiliar.php";

        menu(CTX_DEPART);

        $dept_no = filter_input(INPUT_POST, "dept_no");

        if ($dept_no !== null) {
            $pdo = conectar_bd();
            $orden = $pdo->prepare("delete from depart where dept_no = :dept_no");
            $orden->execute([':dept_no' => $dept_no]);
            header("Location: index.php");
        }

        $dept_no = filter_input(INPUT_GET, "dept_no");

        if ($dept_no === null) {
            header("Location: index.php");
        }

        $dept_no = trim($dept_no);
        $pdo = conectar_bd();

        if (empty(buscar_por_dept_no($pdo, $dept_no))) { ?>
            <h3>Error: el departamento <?= htmlentities($dept_no) ?> no existe</h3>
            <a href="index.php" class="btn btn-warning" role="button">Volver</a><?php
        } else { ?>
            <h3>¿Seguro que quiere borrar el departamento <?= htmlentities($dept_no) ?>?</h3>
            <form action="" method="post">
                <input type="hidden" name="dept_no" value="<?= htmlentities($dept_no) ?>" />
                <button type="submit" class="btn btn-default">Sí</button>
                <a href="index.php" class="btn btn-warning" role="button">No</a>
            </form><?php
        } ?>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
