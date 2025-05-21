<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

if ($method == 'POST' && $path == '/register') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing username or password"]);
        exit();
    }

    $username = htmlspecialchars($data['username']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(username, password) VALUES (:username, :password)");
    try {
        $stmt->execute(['username' => $username, 'password' => $password]);
        http_response_code(201);
        echo json_encode(["message" => "User registered successfully"]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(["error" => "Username might already exist"]);
    }
    exit();
}

if ($method == 'POST' && $path == '/login') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing username or password"]);
        exit();
    }

    $username = htmlspecialchars($data['username']);
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login successful"]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
    exit();
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
