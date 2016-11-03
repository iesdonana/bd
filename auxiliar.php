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

/**
 * Muestra un mensaje de saludo.
 * @param  string $nombre   El nombre de la persona
 * @param  string $telefono El teléfono de la persona
 */
function saludar(string $nombre, string $telefono)
{ ?>
    <h2>Hola, <?= htmlentities($nombre) ?>.
        Tu teléfono es <?= htmlentities($telefono) ?></h2><?php
}

function param_falta($param, $humano, &$error)
{
    if ($param === null) {
        $error[] = "Falta el campo $humano";
        return true;
    }
    return false;
}

function param_longmax($param, $humano, $max, &$error)
{
    if (mb_strlen($param) > $max) {
        $error[] = "El campo $humano no puede superar los $max caracteres";
        return true;
    }
    return false;
}

function param_vacio($param, $humano, &$error)
{
    if ($param === "") {
        $error[] = "El campo $humano no puede estar vacío";
        return true;
    }
    return false;
}

function comprobar_nombre($nombre, $humano, &$error)
{
    if (param_falta($nombre, $humano, $error) ||
        param_longmax($nombre, $humano, 50, $error) ||
        param_vacio($nombre, $humano, $error)) {
        return;
    }
}

function comprobar_telefono($telefono, $humano, &$error)
{
    if (param_falta($telefono, $humano, $error) ||
        param_longmax($telefono, $humano, 9, $error)) {
        return;
    }

    if (filter_var($telefono, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 100000000,
            'max_range' => 999999999
        ]
    ]) === false) {
        $error[] = "El campo $humano debe ser un número de 9 dígitos";
    }
}

function comprobar_errores($error)
{
    if (!empty($error)) {
        throw new Exception;
    }
}
/**
 * Comprueba si estan los valores
 * @param  [array] $params [almacena valores]
 * @return devuelve true si existe un valor nulo
 */
function comprobar_existen($params)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return true;
        }
    }
    throw new Exception;
}
/**
 * Comprueba el número del departamento
 * @param  [string] $dept_no [número de departamento]
 * @param  array  $error  [almacena errores]
 */
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
/**
 * Comprueba y trata el nombre del departamento
 * @param  [string] $dnombre [nombre del departamento]
 * @param  array  $error   [almacena errores]
 */
function comprobar_dnombre(&$dnombre, array &$error)
{
    if ($dnombre === null) {
        throw new Exception;
    }

    $dnombre = trim($dnombre);
    $dnombre = mb_strtoupper($dnombre);

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}



/**
 * [comprobar_loc comprueba y trara características de la función]
 * @param  [string] $loc   [localidad del departamento]
 * @param  array  $error [almacena errores]
 */
function comprobar_loc(&$loc, array &$error)
{
    if ($loc === null) {
        throw new Exception;
    }

    $loc = trim($loc);
    $loc = mb_strtoupper($loc);

    if (mb_strlen($loc) > 50) {
        $error[] = "La localidad debe de tener menos de 50 caracteres";
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
/**
 * Conecta con la base de datos
 * @return PDO [description]
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'ricardo',
        'ricardo'
    );
}

function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_y_dnombre($pdo, $dept_no, "");
}

/**
 * @param  PDO    $pdo     [objeto de la clase pdo]
 * @param  string $dept_no [número del departamento]
 * @param  string $dnombre [nombre del departamento]
 * @return array           [devuelve un array con los resultados de la consulta]
 */
function buscar_por_dept_no_y_dnombre(
    PDO $pdo,
    string $dept_no,
    string $dnombre
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
/**
 * muestra la tabla por pantalla
 * @param  array  $result [almacena los resultados de la consulta]
 */
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
                    <td><?= htmlentities($fila['dept_no']) ?></td>
                    <td><?= htmlentities($fila['dnombre']) ?></td>
                    <td><?= htmlentities($fila['loc']) ?></td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}
