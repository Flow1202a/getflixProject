<?php
global $pdo, $query;
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Accès refusé : Vous n'êtes pas connecté.");
}

require_once '../includes/db_connect.php';

// Supprimer un message
if (isset($_GET['delete'])) {
    $message_id = $_GET['delete'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if ($role == 'admin') {
        // L'administrateur peut supprimer n'importe quel message
        $query = "DELETE FROM messages WHERE id = :id";
        $params = [':id' => $message_id];
    } else {
        // Un utilisateur normal ne peut supprimer que ses propres messages
        $query = "DELETE FROM messages WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $message_id,
            ':user_id' => $user_id
        ];
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            echo "Message supprimé avec succès.";
        } else {
            echo "Erreur : Le message n'a pas pu être supprimé (soit il n'existe pas, soit vous n'avez pas les droits).";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer les messages pour les afficher
try {
    if ($_SESSION['role'] == 'admin') {
        // Si l'utilisateur est un admin, il peut voir tous les messages
        $query = "SELECT messages.id, users.username, messages.content, messages.created_at 
                  FROM messages 
                  JOIN users ON messages.user_id = users.id";
    } else {
        // Sinon, il ne peut voir que ses propres messages
        $query = "SELECT messages.id, users.username, messages.content, messages.created_at 
                  FROM messages 
                  JOIN users ON messages.user_id = users.id 
                  WHERE user_id = :user_id";
    }

    $stmt = $pdo->prepare($query);

    if ($_SESSION['role'] != 'admin') {
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    }

    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="logout">
    <a href="../includes/logout.php">Se déconnecter</a>
</div>
<h1>Gestion des Messages</h1>
<p><a href="../public/index.php">Retour à l'accueil</a></p>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Utilisateur</th>
        <th>Message</th>
        <th>Date de Création</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['id']); ?></td>
                <td><?php echo htmlspecialchars($message['username']); ?></td>
                <td><?php echo htmlspecialchars($message['content']); ?></td>
                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                <td><a href="back_office.php?delete=<?php echo $message['id']; ?>">Supprimer</a></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">Aucun message trouvé.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
