<?php
global $pdo;
session_start();
    require_once('db_connect.php');

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['role'])) {
        die("Accès refusé");
    }

    // Supprimer un message
    if (isset($_GET['delete'])) {
        $message_id = $_GET['delete'];
        $user_id = $_SESSION['users_id'];
        $role = $_SESSION['role'];

        // Si l'utilisateur est un admin, il peut supprimer n'importe quel message
        if ($role == 1) {
            $query = "DELETE FROM messages WHERE id = :id";
            $params = [':id' => $message_id];
        } else {
            // Si l'utilisateur est un utilisateur normal, il ne peut supprimer que ses propres messages
            $query = "DELETE FROM messages WHERE id = :id AND user_id = :users_id";
            $params = [
                ':id' => $message_id,
                ':users_id' => $user_id
            ];
        }

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            header("Location: back_office.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
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
            .delete-btn {
                color: red;
                cursor: pointer;
            }
            .delete-btn:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
    <header>
        <h1>Gestion des Messages</h1>
    </header>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <th>Message</th>
                <th>Supprimer</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    try {
                        // Requête pour récupérer les messages avec les informations de l'utilisateur
                        $stmt = $pdo->query("SELECT messages.id, messages.created_at, users.username, users.email, messages.content 
                                                     FROM messages 
                                                     JOIN users ON messages.user_id = users.id");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>{$row['created_at']}</td>";
                            echo "<td>{$row['username']}</td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td>{$row['content']}</td>";
                            // Le lien de suppression est toujours affiché, mais la suppression ne fonctionnera que si l'utilisateur est autorisé
                            echo "<td><a class='delete-btn' href='back_office.php?delete={$row['id']}'>X</a></td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                }
                ?>
            </tbody>
        </table>
    </body>
</html>