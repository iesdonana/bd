<?php

define("ESC_CONSULTA", 0);
define("ESC_INSERTAR", 1);
define("ESC_MODIFICAR", 2);
define("CTX_DEPART", 0);
define("CTX_LOCALIDADES", 1);
define("CTX_LOGIN", 2);
define("CTX_USUARIO", 3);

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
 * Comprueba si hay errores
 * @param  array $error El array que contiene los errores
 */
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

/**
 * Comprueba que los datos del número de departamento son correctos
 * @param  string $dept_no El numero del departamento
 * @param  array  $error   El array que contiene los errores
 */
function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{

    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) . " ya existe.";
        }
    } elseif ($escenario === ESC_MODIFICAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif ($dept_no !== $dept_no_viejo && !empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) . " ya existe.";
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
 * Comprueba que los datos del nombre de departamento son correctos
 * @param  string $dnombre El nombre del departamento
 * @param  array  $error   El array que contiene los errores
 */
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

/**
 * Comprueba que los datos de la localidad de departamento son correctos
 * @param  string $loc     La localidad del departamento
 * @param  array  $error   El array que contiene los errores
 */
function comprobar_loc(&$loc, array &$error, $escenario = ESC_CONSULTA)
{
    $loc = mb_strtoupper(trim($loc));

    if ($escenario == ESC_INSERTAR && $loc === "") {
        $error[] = "El nombre es obligatorio";
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

/**
 * Comprueba si existe el departamento
 * @param  array  $result El array de los resultados de la búsqueda
 * @param  array  $error  El array que contiene los errores
 */
function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}

/**
 * Conecta con la base de datos
 * @return PDO La conexión con la base de datos
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'joseluis',
        'joseluis'
    );
}

function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_dnombre_y_localidad_id($pdo, $dept_no, "", "");
}

/**
 * Realiza la busqueda en la base de datos con los datos recibidos
 * @param  PDO    $pdo     La conexión con la base de datos
 * @param  string $dept_no El número del departamento
 * @param  string $dnombre El nombre del departamento
 * @param  string $loc     La localidad del departamento
 * @return array           El array con los datos encontrados en la búsqueda
 */
function buscar_por_dept_no_dnombre_y_localidad_id(
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

function buscar_por_loc(PDO $pdo, string $loc): array
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
    $orden = $pdo->prepare("select * from localidades
                                where id = :localidad_id");
    $orden->execute([':localidad_id' => $localidad_id]);
    return $orden->fetch();
}

/**
 * Muestra por pantalla el resultado de la tabla
 * @param  array  $result El array de los resultados de la búsqueda
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
                        $dept_no = htmlentities($fila['dept_no']) ?>
                        <tr>
                            <td><?= $dept_no ?></td>
                            <td><?= htmlentities($fila['dnombre']) ?></td>
                            <td><?= htmlentities($fila['loc']) ?></td>
                            <td><a href="borrar.php?dept_no=<?= $dept_no ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                                <a href="modificar.php?dept_no=<?= $dept_no ?>" class="btn btn-info btn-xs" role="button">Modificar</a>
                                <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a></td>
                        </tr><?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div><?php
}

/**
 * Muestra por pantalla el resultado de la tabla de localidades
 * @param  array  $result El array de los resultados de la búsqueda
 */
function dibujar_tabla_loc(array $result)
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
                        $id = htmlentities($fila['id']) ?>
                        <tr>
                            <td><?= htmlentities($fila['loc']) ?></td>
                            <td><a href="borrar.php?localidad_id=<?= $id ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                                <a href="modificar.php?localidad_id=<?= $id ?>" class="btn btn-info btn-xs" role="button">Modificar</a>
                                <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a></td>
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
        <option value=""></option> <?php
        foreach ($localidades as $loc) { ?>
            <option value="<?= htmlentities($loc['id']) ?>" <?=
            ($loc['id'] == $localidad_id) ? "selected" : "" ?>
            > <?= htmlentities($loc['loc']) ?> </option><?php
        } ?>
    </select> <?php
}


function menu($contexto = null)
{ ?>
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
          <a class="navbar-brand" href="/iesdonana/bd/index.php">Menú principal</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class=<?= ($contexto === CTX_DEPART) ? "active" : "" ?>><a href="/iesdonana/bd/depart">Departamentos</a></li>
            <li class=<?= ($contexto === CTX_LOCALIDADES) ? "active" : "" ?>><a href="/iesdonana/bd/localidades">Localidades</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <?php
                  if (isset($_SESSION['login'])) { ?>
                      <li class="dropdown <?= ($contexto === CTX_USUARIO) ? "active" : "" ?>">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                             role="button" aria-haspopup="true" aria-expanded="false">
                                <?= htmlentities($_SESSION['login']); ?>
                                <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu">
                            <li><a href="/iesdonana/bd/comunes/cambiar_nombre.php">Cambiar nombre</a></li>
                            <li><a href="/iesdonana/bd/comunes/cambiar_password.php">Cambiar contraseña</a></li>
                          </ul>
                      </li>
                      <li>
                          <a href="/iesdonana/bd/comunes/logout.php">Logout</a>
                      </li> <?php
                  } else { ?>
                      <li class=<?= ($contexto === CTX_LOGIN) ? "active" : "" ?>>
                          <a href="/iesdonana/bd/comunes/login.php">Login</a>
                      </li> <?php
                  } ?>

          </ul>
        </div>  <!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
  </nav> <?php
}

function comprobar_credenciales(PDO $pdo, $user, $pass, array &$error)
{
    $orden = $pdo->prepare("select * from usuarios where nombre = :user");
    $orden->execute([':user' => $user]);
    $result = $orden->fetch();

    if (empty($result) || !password_verify($pass, $result['pass'])) {
        $error[] = "Credenciales incorrectas";
    }
}

function comprobar_logueado()
{
    if (!usuario_logueado()) {
        header("Location: /iesdonana/bd/comunes/login.php");
    }
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
