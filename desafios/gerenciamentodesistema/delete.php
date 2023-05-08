// delete.php

require_once 'config.php';
require_once 'functions.php';

$id = $_GET['id'];

if (!$id) {
    redirect('index.php');
}

$pdo = connectDb();

$query = "DELETE FROM content WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':id', $id);
$stmt->execute();

redirect('index.php');
