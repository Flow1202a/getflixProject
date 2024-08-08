<?php

require_once('../includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);  // Sanitize username
    $password = $_POST['password'];

try {


    // Prepare a secure SQL statement
    $sql = "SELECT users.id, users.username, users.email, users.role, user_conf.password
            FROM users
            INNER JOIN user_conf ON users.id = user_conf.user_id
            WHERE users.username = :username OR users.email = :username";
    $stmt = $pdo->prepare($sql);

    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // Execute the prepared statement
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      // Login successful
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    header("Location: admin.php");
      exit;  // Prevent further script execution
    } else {
      // Login failed - avoid specific error messages for security
    echo "Invalid login credentials.";
    }
    } catch(PDOException $e) {
    // Handle database errors gracefully
    echo "Database error: " . $e->getMessage();
}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <form action="login.php" method="post">
        <label for="username">Nom d'utilisateur ou Email:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Se connecter">
    </form>
</body>
</html>