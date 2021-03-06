<?php

define("ESC_CONSULTA", 0);
define("ESC_INSERTAR", 1);
define("ESC_MODIFICAR", 2);
define("CTX_DEPART", 0);
define("CTX_LOCALIDADES", 1);
define("CTX_LOGIN", 2);
define("CTX_CAMBIA_PASSWORD", 3);
define("RUTA_ASSETS", "/bd/assets/");
define("RUTA_IMG", RUTA_ASSETS . "img/");

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
        <div class="alert alert-danger" role="alert">
            Error: <?= htmlentities($e) ?>
        </div><?php
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

function comprobar_existen($params)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return true;
        }
    }
    throw new Exception;
}

function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{
    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) .
                       " ya existe";
        }
    } elseif ($escenario === ESC_MODIFICAR) {
        if ($dept_no === "") {
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

function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    $dnombre = mb_strtoupper(trim($dnombre));

    if ($escenario === ESC_INSERTAR && $dnombre === "") {
        $error[] = "El nombre es obligatorio";
    }

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

function comprobar_loc(&$loc, array &$error, $escenario = ESC_CONSULTA)
{
    $loc = mb_strtoupper(trim($loc));

    if ($escenario === ESC_INSERTAR && $loc === "") {
        $error[] = "La localidad es obligatoria";
    }

    if (mb_strlen($loc) > 100) {
        $error[] = "La localidad no puede tener más de 100 caracteres";
    }
}

function comprobar_localidad_id(&$localidad_id, PDO $pdo, array &$error)
{
    $localidad_id = trim($localidad_id);

    if ($localidad_id !== "") {
        $orden = $pdo->prepare("select * from localidades where id = :localidad_id");
        $orden->execute([':localidad_id' => $localidad_id]);
        $result = $orden->fetchAll();

        if (empty($result)) {
            $error[] = "No existe la localidad indicada";
        }
    } else {
        $localidad_id = null;
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
        if ($p !== null) {
            return;
        }
    }
    $error[] = "Debe indicar al menos un criterio de búsqueda";
}

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

function buscar_por_dept_no_y_dnombre(
    PDO $pdo,
    string $dept_no,
    string $dnombre
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
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_dept_no_dnombre_localidad_id(
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

    if ($loc !== "" && $loc !== null) {
        $sql .= " and loc ilike :loc";
        $params[':loc'] = "%$loc%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_localidad_id(PDO $pdo, $localidad_id): array
{
    $orden = $pdo->prepare("select *
                              from localidades
                             where id = :localidad_id");
    $orden->execute([':localidad_id' => $localidad_id]);
    return $orden->fetch();
}

/**
 * Dibuja la tabla con el resultado de la consulta
 * @param  array  $result Matriz de filas x columnas con el resultado
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
                        $dept_no = htmlentities($fila['dept_no']); ?>
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
            <table class="table">
                <thead>
                    <th>Localidad</th>
                    <th>Operaciones</th>
                </thead>
                <tbody><?php
                    foreach ($result as $fila) {
                        $id = htmlentities($fila['id']); ?>
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

function obtener_localidades(PDO $pdo): array
{
    $orden = $pdo->prepare("select * from localidades");
    $orden->execute();
    return $orden->fetchAll();
}

function lista_localidades(array $localidades, $localidad_id = null)
{ ?>
    <select name="localidad_id" id="localidad_id" class="form-control">
        <option value=""></option><?php
        foreach ($localidades as $loc) { ?>
            <option value="<?= htmlentities($loc['id']) ?>" <?=
                ($loc['id'] == $localidad_id) ? "selected" : "" ?> >
                <?= htmlentities($loc['loc']) ?>
            </option><?php
        } ?>
    </select><?php
}

function menu($contexto = null)
{ ?>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/bd/">Menú principal</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li <?= ($contexto === CTX_DEPART) ? 'class="active"' : '' ?> >
                        <a href="/bd/depart">Departamentos</a>
                    </li>
                    <li <?= ($contexto === CTX_LOCALIDADES) ? 'class="active"' : '' ?> >
                        <a href="/bd/localidades">Localidades</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right"><?php
                    if (isset($_SESSION['login'])) { ?>
                        <li class="dropdown<?= ($contexto === CTX_CAMBIA_PASSWORD) ? ' active' : '' ?>">
                            <a href="#"
                               class="dropdown-toggle"
                               data-toggle="dropdown"
                               role="button"
                               aria-haspopup="true"
                               aria-expanded="false">
                                <?= htmlentities($_SESSION['login']) ?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/bd/comunes/cambiar_nombre.php">
                                        Cambiar nombre
                                    </a>
                                </li>
                                <li>
                                    <a href="/bd/comunes/cambiar_password.php">
                                        Cambiar contraseña
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="/bd/comunes/logout.php">Logout</a>
                        </li><?php
                    } else { ?>
                        <li <?= ($contexto === CTX_LOGIN) ? 'class="active"' : '' ?> >
                            <a href="/bd/comunes/login.php">Login</a>
                        </li><?php
                    } ?>
                </ul>
            </div>
        </div>
    </nav><?php
}

function comprobar_credenciales(PDO $pdo, $login, $pass, array &$error)
{
    $orden = $pdo->prepare("select * from usuarios where nombre = :login");
    $orden->execute([':login' => $login]);
    $result = $orden->fetch();
    if (empty($result) || !password_verify($pass, $result['pass'])) {
        $error[] = "Credenciales incorrectas";
    }
}

function comprobar_logueado()
{
    if (!usuario_logueado()) {
        header("Location: /bd/comunes/login.php");
        return false;
    }
    return true;
}

function usuario_logueado(): bool
{
    return isset($_SESSION['login']);
}

function comprobar_vieja(PDO $pdo, $vieja, array &$error)
{
    $orden = $pdo->prepare("select pass
                              from usuarios
                             where nombre = :login");
    $orden->execute([':login' => $_SESSION['login']]);
    $pass = $orden->fetchColumn();
    if (!password_verify($vieja, $pass)) {
        $error[] = "La contraseña anterior es incorrecta";
    }
}

function comprobar_nueva($nueva, $nueva_confirm, array &$error)
{
    if ($nueva === "") {
        $error[] = "La contraseña no puede ser vacía";
    } elseif ($nueva !== $nueva_confirm) {
        $error[] = "Las contraseñas no coinciden";
    }
}

function comprobar_nombre_usuario(PDO $pdo, &$nombre, $id, array &$error)
{
    $nombre = trim($nombre);

    if ($nombre === "") {
        $error[] = "El nombre no puede ser vacío";
    } elseif (mb_strlen($nombre) > 20) {
        $error[] = "El nombre no puede tener más de 20 caracteres";
    } else {
        $orden = $pdo->prepare("select count(*)
                                  from usuarios
                                 where nombre = :nombre and
                                       id != :id");
        $orden->execute([':nombre' => $nombre, ':id' => $id]);
        if ($orden->fetchColumn() > 0) {
            $error[] = "El nombre ya está en uso";
        }
    }
}
