<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Modificar un departamento</title>
    </head>
    <body><?php
        require "auxiliar.php";

        $dept_no = filter_input(INPUT_GET, "dept_no");

        if ($dept_no !== null){
            $pdo = conectar_bd();
            $result = buscar_por_dept_no($pdo, $dept_no);
            if (empty($result)){
                header("Location: bd.php");
            }
            $result  = $result[0];
            $dnombre = $result['dnombre'];
            $loc     = $result['loc'];
        } else {
            $dept_no_viejo = filter_input(INPUT_POST, "dept_no_viejo");
            $dept_no = filter_input(INPUT_POST, "dept_no");
            $dnombre = filter_input(INPUT_POST, "dnombre");
            $loc = filter_input(INPUT_POST, "loc");

            try{
                $error = [];
                comprobar_existen([$dept_no, $dnombre, $loc]);
                comprobar_dept_no($dept_no, $error, ESC_MODIFICAR, $dept_no_viejo);
                comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
                comprobar_loc($loc, $error);
                comprobar_errores($error);
                $pdo = conectar_bd();
                $orden = $pdo->prepare("update depart
                                        set dept_no = :dept_no,
                                            dnombre = :dnombre,
                                            loc     = :loc
                                      where dept_no = :dept_no_viejo");
                $orden->execute([
                        ':dept_no' => $dept_no,
                        ':dnombre' => $dnombre,
                        ':loc' => $loc,
                        ':dept_no_viejo' => $dept_no_viejo
                ]);

                header("Location: bd.php");
            } catch (PDOException $e) { ?>
                <h3>Error de conexión a la base de datos</h3><?php
            } catch (Exception $e) {
                mostrar_errores($error);
            }
        }?>


        <form action="modificar.php" method="post">
            <input type="hidden" name="dept_no_viejo" value="<?= htmlentities($dept_no)?>">
            <label for="dept_no">Número de departamento: *</label>
            <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" /><br/>
            <label for="dnombre">Nombre de departamento: *</label>
            <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="loc">Localidad:</label>
            <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
            <input type="submit" value="Modificar" />
            <a href="bd.php" role="button">Cancelar</a>
        </form>
    </body>
</html>
