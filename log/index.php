<?php
session_start(); // Iniciar la sesión

// Comprobar si el usuario ya ha iniciado sesión
if (isset($_SESSION['usuario'])) {
  header('Location: perfil.php');
  exit;
}

// Comprobar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Conectar a la base de datos
  $mysqli = new mysqli('localhost', 'usuario', 'contraseña', 'nombre_de_la_bd');

  // Verificar si la conexión fue exitosa
  if ($mysqli->connect_error) {
    die('Error de conexión (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
  }

  // Escapar los valores del formulario para evitar inyección de SQL
  $nombre_usuario = $mysqli->real_escape_string($_POST['nombre_usuario']);
  $contrasena = $_POST['contrasena'];

  // Buscar el usuario en la base de datos
  $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$nombre_usuario'";
  $resultado = $mysqli->query($sql);

  // Verificar si se encontró un usuario con ese nombre
  if ($resultado->num_rows === 1) {
    // Obtener los datos del usuario de la base de datos
    $fila = $resultado->fetch_assoc();
    $contrasena_hash = $fila['contrasena'];

    // Verificar la contraseña
    if (password_verify($contrasena, $contrasena_hash)) {
      // Iniciar la sesión y redirigir al perfil del usuario
      $_SESSION['usuario'] = $nombre_usuario;
      header('Location: perfil.php');
      exit;
    } else {
      $error = 'Contraseña incorrecta';
    }
  } else {
    $error = 'Usuario no encontrado';
  }

  // Cerrar la conexión a la base de datos
  $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
</head>
<body>
  <h1>Iniciar sesión</h1>

  <?php if (isset($error)): ?>
    <p><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nombre de usuario:</label><br>
    <input type="text" name="nombre_usuario"><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="contrasena"><br><br>

    <input type="submit" value="Iniciar sesión">
  </form>
</body>
</html>
