// edit.php

require_once 'config.php';
require_once 'functions.php';

$id = $_GET['id'];

if (!$id) {
    redirect('index.php');
}

$pdo = connectDb();

$query = "SELECT * FROM content WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':id', $id);
$stmt->execute();

$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];

    $query = "UPDATE content SET title = :title, body = :body WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':body', $body);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    redirect('index.php');
}

?>

<form method="post">
    <label for="title">Title</label>
    <input type="text" name="title" value="<?php echo $content['title'] ?>" required>

    <label for="body">Body</label>
    <textarea name="body" required><?php echo $content['body'] ?></textarea>

    <button type="submit">Save</button>
</form>
