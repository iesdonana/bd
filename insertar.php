<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Insertar un departamento</title>
    </head>
    <body><?php
        require "auxiliar.php";

        $dept_no = filter_input(INPUT_POST, "dept_no");
        $dnombre = filter_input(INPUT_POST, "dnombre");
        $loc = filter_input(INPUT_POST, "loc");

        try {
            $error = [];
            comprobar_existen([$dept_no, $dnombre, $loc]);
            comprobar_dept_no($dept_no, $error, ESC_INSERTAR);
            comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
            comprobar_loc($loc, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $orden = $pdo->prepare("insert into depart (dept_no,dnombre,loc)
                                    values (:dept_no, :dnombre, :loc)");
            $orden->execute([
                ':dept_no' => $dept_no,
                ':dnombre' => $dnombre,
                ':loc' => $loc
            ]);
            header("Location: bd.php");
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        }

     ?>
        <form action="" method="post">
            <label for="dept_no">Número de departamento *:</label>
            <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" /><br/>
            <label for="dnombre">Nombre de departamento *:</label>
            <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="loc">Localidad del departamento:</label>
            <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
            <input type="submit" value="Insertar" />
            <input type="reset" value="Limpiar" />
            <a href="bd.php">Cancelar</a>
        </form>
    </body>
</html>
