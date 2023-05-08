<?php

require_once 'config.php';

// Verifica se o usuário está logado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Exibe a lista de artigos
$articles = getArticles();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerenciador de Conteúdo</title>
</head>
<body>
    <h1>Gerenciador de Conteúdo</h1>

    <a href="new_article.php">Novo Artigo</a>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?php echo $article['title']; ?></td>
                    <td><?php echo $article['date']; ?></td>
                    <td>
                        <a href="edit_article.php?id=<?php echo $article['id']; ?>">Editar</a>
                        <a href="delete_article.php?id=<?php echo $article['id']; ?>">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="logout.php">Sair</a>
</body>
</html>
