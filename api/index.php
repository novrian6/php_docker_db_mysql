<?php
$dsn = 'mysql:host=db;dbname=testdb;charset=utf8mb4';
$username = 'root';
$password = 'rootpassword';

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the `messages` table if it doesn't exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createTableSQL);

    // Basic routing based on URI
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Handling POST request to add a message
    if ($uri === '/add-message' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['content'])) {
            $content = $_POST['content'];

            // Insert message into the database
            $stmt = $pdo->prepare("INSERT INTO messages (content) VALUES (:content)");
            $stmt->bindParam(':content', $content);
            $stmt->execute();

            echo "Message added: $content";
        } else {
            http_response_code(400);
            echo "Content is required.";
        }
    }
    // Handling GET request to view messages
    elseif ($uri === '/view-messages' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch messages from the database
        $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($messages) {
            foreach ($messages as $message) {
                echo "ID: " . $message['id'] . "<br>";
                echo "Message: " . $message['content'] . "<br>";
                echo "Created at: " . $message['created_at'] . "<br><br>";
            }
        } else {
            echo "No messages found.";
        }
    } else {
        http_response_code(404);
        echo "Route not found.";
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
}
?>