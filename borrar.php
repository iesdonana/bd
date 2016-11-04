<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar un departamento</title>
    </head>
    <body><?php
        require "auxiliar.php";

        $dept_no = filter_input(INPUT_POST,'dept_no');

        if ($dept_no !== null) {
            //hacer borrado
            $pdo = conectar_bd();
            $orden = $pdo->prepare("delete from depart where dept_no = :dept_no");
            $orden-> execute([':dept_no' => $dept_no]);
            header("Location: bd.php");
        }

        $dept_no = filter_input(INPUT_GET,'dept_no');

        if ($dept_no === null) {
            header("Location: bd.php");
        }

        $dept_no = trim($dept_no); ?>
        <h3>¿Seguro que quiere borrar el departamento <?= htmlentities($dept_no) ?>?</h3>
        <form action="" method="post">
            <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
            <input type="submit" value="Si">
            <input type="button" value="No" onclick="location.assign('bd.php');">
        </form>
    </body>
</html>
