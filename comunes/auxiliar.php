<?php

define("ESC_CONSULTA", 0);
define("ESC_INSERTAR", 1);
define("ESC_MODIFICAR", 2);
define("CTX_LOGIN", 3);

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

/**
 * Comprueba si el array de errores está vacío (no contiene ningún error).
 * @param  array $error Array con los errores.
 */
function comprobar_errores(array $error)
{
    if (!empty($error)) {
        throw new Exception;
    }
}

/**
 * Comprueba si hay algun valor en el array, que sea distinto de null.
 * @param  array $params  Array con los distintos valores que queremos comprobar
 * @return bool           true si hay alguno distinto de null.
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
 * Comprueba que el número de departamento contenga valores válidos.
 * @param  string $dept_no  Número de departamento.
 * @param  array  $error    Array con los errores.
 */
function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{
    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR)
    {
        if ($dept_no === ""){
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))){
            $error[] = "El departamento " . htmlentities($dept_no) .
                        " ya existe";
        }
    } elseif ($escenario === ESC_MODIFICAR){
        if ($dept_no === ""){
            $error[] = "El número es obligatorio";
        } elseif ($dept_no !== $dept_no_viejo &&
                !empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
                    $error[] = "El departamento " . htmlentities($dept_no) .
                                " ya existe";
            }
        }

    if ($dept_no !== "" && !ctype_digit($dept_no)) {
        $error[] = "El número de departamento debe ser un número";
    }

    if (mb_strlen($dept_no) > 2) {
        $error[] = "El número de departamento debe contener 1 ó 2 dígitos";
    }
}

/**
 * Comprueba que el nombre de departamento contenga valores válidos.
 * @param  string   $dnombre      Nombre de departamento.
 * @param  array    $error        Array con los errores.
 * @param  integer  $escenario    Constante que vale 0 ó 1 segun si estamos
 *                                en consulta o inserción.
 */
function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    $dnombre = strtoupper(trim($dnombre));

    if ($escenario === ESC_INSERTAR && $dnombre === ""){
        $error[] = "El nombre es obligatorio";
    }

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

function comprobar_loc(&$loc, array &$error, $escenario = ESC_CONSULTA)
{
    $loc = strtoupper(trim($loc));

    if ($escenario === ESC_INSERTAR && $loc === ""){
        $error[] = "La localidad es obligatoria";
    }

    if (mb_strlen($loc) > 100) {
        $error[] = "La localidad no puede tener más de 100 caracteres";
    }
}

function comprobar_localidad_id(&$localidad_id, PDO $pdo, array &$error)
{
    $localidad_id = trim($localidad_id);

    if ($localidad_id !== ""){
        $orden = $pdo->prepare("select * from localidades where id = :localidad_id");
        $orden->execute([':localidad_id' => $localidad_id]);
        $result = $orden->fetchAll();

        if (empty($result)){
            $error[] = "No existe la localidad indicada";
        }
    } else {
        $localidad_id = null;
    }
}


/**
 * Comprueba si el array que contiene las filas resultantes de la consulta,
 * está vacío
 * @param  array  $result  Array con las filas resultantes de la consulta.
 * @param  array  $error   Array con los errores.
 */
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
 * @return PDO  Objeto con la conexión.
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'celu',
        'celu'
    );
}

/**
 * Realiza una búsqueda por dept_no, y devuelve un array con el resultado.
 * @param  PDO    $pdo      Objeto con la conexión.
 * @param  string $dept_no  Número de departamento.
 * @return array            Array con el resultado de la consulta.
 */
function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_y_dnombre_y_localidad_id($pdo, $dept_no, "", "");
}

/**
 * Realiza una búsqueda por dept_no, dnombre y loc (Cualquiera de ellos)
 * @param  PDO    $pdo           Objeto con la conexión.
 * @param  string $dept_no       Número de departamento.
 * @param  string $dnombre       Nombre de departamento.
 * @param  string $localidad_id  Localidad.
 * @return array                 Array con el resultado de la consulta.
 */
function buscar_por_dept_no_y_dnombre_y_localidad_id(
    PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $localidad_id = null
): array {
    $sql = "select * from depart_v where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre ilike :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    if ($localidad_id !== "" && $localidad_id !== null) {
        $sql .= " and localidad_id = :localidad_id";
        $params[':localidad_id'] = $localidad_id;
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}


function buscar_por_loc(PDO $pdo, string $loc = null): array
{
    $sql = "select * from localidades where true";
    $params = [];

    if ($loc !== "" && $loc !== null){
        $sql .= " and loc ilike :loc";
        $params[':loc'] = "%$loc%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_localidad_id(PDO $pdo, $localidad_id): array
{
    $sql = "select * from localidades
            where id = :localidad_id";

    $orden = $pdo->prepare($sql);
    $orden->execute([':localidad_id' => $localidad_id]);
    return $orden->fetch();
    //En este caso haremos fetch, ya que solo devolveremos una fila.
}

function obtener_localidades(PDO $pdo): array
{
    $orden = $pdo->prepare("select * from localidades");
    $orden->execute();
    return $orden->fetchAll();
}

/**
 * Dibuja la tabla con los resultados de una consulta, pasados por parámetro
 * @param  array  $result  Array con el resultado de la consulta.
 */
function dibujar_tabla(array $result)
{ ?>
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <table class="table">
                <thead>
                    <th>Número</th>
                    <th>Nombre</th>
                    <th>Localidad</th>
                    <th>Operaciones</th>
                </thead>
                <tbody><?php
                    foreach ($result as $fila) {
                        $dept_no = htmlentities($fila['dept_no']);?>
                        <tr>
                            <td><?= $dept_no ?></td>
                            <td><?= htmlentities($fila['dnombre']) ?></td>
                            <td><?= htmlentities($fila['loc']) ?></td>
                            <td>
                                <a href="borrar.php?dept_no=<?= $dept_no ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                                <a href="modificar.php?dept_no=<?= $dept_no ?>" class="btn btn-info btn-xs" role="button">Modificar</a>
                                <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a>

                            </td>
                        </tr><?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div><?php
}

function dibujar_tabla_localidades(array $result)
{ ?>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <table  class="table">
                <thead>
                    <th>Localidad</th>
                    <th>Operaciones</th>
                </thead>
                <tbody><?php
                    foreach ($result as $fila) {
                        $id = htmlentities($fila['id']);?>
                        <tr>
                            <td><?= htmlentities($fila['loc']) ?></td>
                            <td>
                                <a href="borrar.php?localidad_id=<?= $id ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                                <a href="modificar.php?localidad_id=<?= $id ?>" class="btn btn-info btn-xs" role="button">Modificar</a>
                                <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a>

                            </td>
                        </tr><?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div><?php
}

function lista_localidades(array $localidades, $localidad_id = null)
{?>
    <select name="localidad_id" id="localidad_id" class="form-control">
        <option value=""></option><?php
        foreach ($localidades as $loc) {?>
            <option value="<?= htmlentities($loc['id']) ?>" <?=
                ($loc['id'] == $localidad_id) ? "selected" : "" ?>
                >
                <?= htmlentities($loc['loc']) ?>
            </option><?php
        }?>
    </select><?php
}

function menu($contexto = null)
{?>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/bd">Menú principal</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li <?= ($contexto === "depart") ? 'class="active"' : ""?>>
                <a href="/bd/depart">Departamentos</a>
            </li>
            <li <?= ($contexto === "localidades") ? 'class="active"' : ""?>>
                <a href="/bd/localidades">Localidades</a>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li <?= ($contexto === CTX_LOGIN) ? 'class="active"' : ""?>><a href="/bd/comunes/login.php">Login</a></li>
          </ul>

        </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav><?php
}
