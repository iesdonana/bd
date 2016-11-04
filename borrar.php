<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar un departamento</title>
    </head>
    <body> <?php
        require "auxiliar.php";
        $dept_no = filter_input(INPUT_POST, "dept_no");

        if ($dept_no !== null) {
            // hacer borrado
            $pdo = conectar_bd();
            $orden = $pdo->prepare("delete from depart where dept_no = :dept_no");
            $orden->execute([':dept_no' => $dept_no]);
            header("Location: bd.php");
        }

        $dept_no = filter_input(INPUT_GET, "dept_no");

        if ($dept_no === null) {
            header("Location: bd.php");
        }

        $dept_no = trim($dept_no);
        $pdo = conectar_bd();

        if (empty(buscar_por_dept_no($pdo, $dept_no))) { ?>
            <h3>Error el departamento <?= htmlentities($dept_no) ?> no existe</h3>
            <a href="bd.php" role="button">Volver</a> <?php
        } else {?>
            <h3>¿Seguro que quiere borrar el departamento <?= htmlentities($dept_no) ?>?</h3>
            <form action="" method="post">
                <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                <button type="submit">Si</button>
                <a href="bd.php" role="button">No</a>
            </form> <?php
        } ?>
    </body>
</html>
