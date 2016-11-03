<?php

function exception_error_handler($severidad, $mensaje, $fichero, $línea) {
    if (!(error_reporting() & $severidad)) {
        // Este código de error no está incluido en error_reporting
        return;
    }
    throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
}
set_error_handler("exception_error_handler");

/**
 * Muestra por la salida los errores del argumento.
 * @param  array $err El array que contiene los errores
 */
function mostrar_errores($err)
{
    foreach ($err as $e) { ?>
        <h3>Error: <?= htmlentities($e) ?></h3><?php
    }
}

function comprobar_errores($error)
{
    if (!empty($error)) {
        throw new Exception;
    }
}

function comprobar_existen($params)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return true;
        }
    }
    throw new Exception;
}

function comprobar_dept_no(&$dept_no, array &$error)
{
    if ($dept_no === null) {
        throw new Exception;
    }

    $dept_no = trim($dept_no);

    if ($dept_no !== "" && !ctype_digit($dept_no)) {
        $error[] = "El número de departamento debe ser un número";
    }

    if (mb_strlen($dept_no) > 2) {
        $error[] = "El número de departamento debe contener 1 ó 2 dígitos";
    }
}

function comprobar_dnombre(&$dnombre, array &$error)
{
    if ($dnombre === null) {
        throw new Exception;
    }

    $dnombre = trim($dnombre);

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

function comprobar_loc(&$loc, array &$error)
{
    if ($loc === null) {
        throw new Exception;
    }

    $loc = trim($loc);

    if (mb_strlen($loc) > 50) {
            $error[] = "El nombre de la localidad no puede tener más de 50 caracteres";
        }
}

function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}

function comprobar_si_hay_uno(array $params, array &$error)
{
    foreach ($params as $p) {
        if ($p !== "") {
            return;
        }
    }
    $error[] = "Debe indicar al menos un criterio de búsqueda";
}

function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'aaron',
        'aaron'
    );
}

function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_y_dnombre($pdo, $dept_no, "");
}

function buscar_por_dept_no_y_dnombre(
    PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $loc
): array {
    $sql = "select * from depart where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre like :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    if ($loc !== "") {
        $sql .= " and loc like :loc";
        $params[':loc'] = "%$loc%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function dibujar_tabla(array $result)
{ ?>
    <table border="1">
        <thead>
            <th>DEPT_NO</th>
            <th>DNOMBRE</th>
            <th>LOC</th>
        </thead>
        <tbody><?php
            foreach ($result as $fila) { ?>
                <tr>
                    <td><?= $fila['dept_no'] ?></td>
                    <td><?= $fila['dnombre'] ?></td>
                    <td><?= $fila['loc'] ?></td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}
