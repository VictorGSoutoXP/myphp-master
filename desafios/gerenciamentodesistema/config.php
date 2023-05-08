<?php

// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'senha');
define('DB_NAME', 'gerenciador_de_conteudo');

// Inicia a sessão
session_start();

// Funções de proteção contra ataques de injeção de SQL
function connect() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        die('Erro ao conectar ao banco de dados: ' . $mysqli->connect_error);
    }
    return $mysqli;
}

function escape($value) {
    $mysqli = connect();
    $value = $mysqli->real_escape_string($value);
    $mysqli->close();
    return $value;
}

// Funções de autenticação
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($email, $password) {
    global $mysqli;

    // Escapa os caracteres especiais
    $email = escape($email);

    // Busca o usuário no banco de dados
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $mysqli->query($query);

    if (!$result || $result->num_rows !== 1) {
        return false;
    }

    // Verifica se a senha está correta
    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    // Define as variáveis de sessão
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];

    return true;
}

// Função de logout
function logout() {
    session_destroy();
}

// Funções de gerenciamento de conteúdo
function getArticles() {
    global $mysqli;
    $query = "SELECT * FROM articles";
    $result = $mysqli->query($query);
    return $result
    >fetch_all(MYSQLI_ASSOC);
}

function getArticle($id) {
global $mysqli;
$id = escape($id);
$query = "SELECT * FROM articles WHERE id = $id";
$result = $mysqli->query($query);
return $result->fetch_assoc();
}

function createArticle($title, $content) {
global $mysqli;
$title = escape($title);
$content = escape($content);
$query = "INSERT INTO articles (title, content) VALUES ('$title', '$content')";
return $mysqli->query($query);
}

function updateArticle($id, $title, $content) {
global $mysqli;
$id = escape($id);
$title = escape($title);
$content = escape($content);
$query = "UPDATE articles SET title = '$title', content = '$content' WHERE id = $id";
return $mysqli->query($query);
}

function deleteArticle($id) {
global $mysqli;
$id = escape($id);
$query = "DELETE FROM articles WHERE id = $id";
return $mysqli->query($query);
}