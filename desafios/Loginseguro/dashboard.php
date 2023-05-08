<?php
session_start();

// Verifica se o usuário está autenticado
if(!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Bem-vindo!</h1>
  <p>Você está autenticado.</p>
  <form method="post" action="logout.php">
    <button type="submit">Sair</button>
  </form>
</body>
</html>
