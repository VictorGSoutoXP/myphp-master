<?php

// Inicializa a sessão
session_start();

// Configurações do banco de dados
$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = 'senha_do_banco';
$dbName = 'nome_do_banco';

// Conecta ao banco de dados
$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

// Verifica se houve erro na conexão
if ($mysqli->connect_error) {
    die('Erro na conexão: ' . $mysqli->connect_error);
}

// Função para escapar caracteres especiais e evitar injeção de SQL e XSS
function escape($str) {
    global $mysqli;
    return htmlspecialchars(mysqli_real_escape_string($mysqli, $str));
}

// Função para gerar um código de autenticação em dois fatores
function generateCode($secret) {
    require_once 'GoogleAuthenticator.php';
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->getCode($secret);
}

// Função para verificar um código de autenticação em dois fatores
function verifyCode($secret, $code) {
    require_once 'GoogleAuthenticator.php';
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->verifyCode($secret, $code);
}

// Função para gerar um código QR para o Google Authenticator
function generateQrCode($name, $secret) {
    $url = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' .
           urlencode('otpauth://totp/' . $name . '?secret=' . $secret);
    return '<img src="' . $url . '" />';
}

// Função para limpar a sessão e redirecionar para a página de login
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Verifica se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Verifica se o usuário ativou o autenticação em dois fatores
function isTwoFactorEnabled() {
    return isset($_SESSION['two_factor_enabled']) && $_SESSION['two_factor_enabled'];
}

// Verifica se o usuário inseriu as credenciais corretas
function login($email, $password, $code = null) {
    global $mysqli;

    // Escapa os caracteres especiais
    $email = escape($email);
    $password = escape($password);

    // Busca o usuário no banco de dados
    $query = "SELECT id, name, email, password, secret FROM users WHERE email = '$email' LIMIT 1";
    $result = $mysqli->query($query);

    if (!$result || $result->num_rows != 1) {
        return false;
    }

    // Verifica se a senha está correta
    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    // Verifica se o autenticação em dois fatores está ativado
    $twoFactorEnabled = isset($user['secret']) && !empty($user['secret']);
    $_SESSION['two_factor_enabled'] = $twoFactorEnabled;

    // Verifica se o código de autenticação em dois fatores está correto
    if ($two

    // Verifica se o usuário já excedeu o limite de tentativas de login
if (isMaxLoginAttemptsExceeded($email)) {
    return false;
}

// Limpa as tentativas de login antigas
clearLoginAttempts($email);

// Armazena o ID do usuário na sessão
$_SESSION['user_id'] = $user['id'];
$_SESSION['name'] = $user['name'];

return true;
}

// Verifica se o usuário já excedeu o limite de tentativas de login
function isMaxLoginAttemptsExceeded($email) {
global $mysqli;
// Escapa os caracteres especiais
$email = escape($email);

// Busca o usuário no banco de dados
$query = "SELECT attempts FROM login_attempts WHERE email = '$email' AND time > DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 1";
$result = $mysqli->query($query);

if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    return $row['attempts'] >= 5;
}

return false;
}

// Registra uma tentativa de login no banco de dados
function registerLoginAttempt($email, $success) {
global $mysqli;
$ip = $_SERVER['REMOTE_ADDR'];
$time = date('Y-m-d H:i:s');
$success = $success ? '1' : '0';

// Escapa os caracteres especiais
$email = escape($email);

// Insere a tentativa de login no banco de dados
$query = "INSERT INTO login_attempts (email, ip, time, success) VALUES ('$email', '$ip', '$time', '$success')";
$mysqli->query($query);
}

// Limpa as tentativas de login antigas
function clearLoginAttempts($email) {
global $mysqli;
// Escapa os caracteres especiais
$email = escape($email);

// Deleta as tentativas de login antigas
$query = "DELETE FROM login_attempts WHERE email = '$email' AND time < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$mysqli->query($query);
}

?>