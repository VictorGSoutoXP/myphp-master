<?php
session_start();

// Verifica se o usuário já está logado
if(isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}

// Conexão com o banco de dados
$conn = mysqli_connect("localhost", "username", "password", "database");

// Verifica se o formulário de login foi submetido
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Consulta o banco de dados para verificar se o usuário existe
  $query = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $query);

  if(mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    // Verifica se a senha está correta
    if(password_verify($password, $user['password'])) {

      // Verifica se a autenticação em dois fatores está habilitada
      if($user['two_factor_enabled']) {
        // Se estiver habilitada, redireciona para a página de autenticação em dois fatores
        $_SESSION['temp_user_id'] = $user['id'];
        header("Location: two_factor.php");
        exit();
      } else {
        // Se não estiver habilitada, faz login normalmente
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
      }

    } else {
      // Senha incorreta
      $error = "Senha incorreta.";
    }

  } else {
    // Usuário não encontrado
    $error = "Usuário não encontrado.";
  }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <?php if(isset($error)) { ?>
    <p><?php echo $error; ?></p>
  <?php } ?>

  <form method="post">
    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Senha:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Entrar</button>
  </form>
</body>
</html>
