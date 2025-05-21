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

if ($method === 'GET' && $path === '/reservations') {
    $stmt = $conn->prepare("SELECT * FROM reservations");
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reservations);
    exit();
}

if ($method === 'POST' && $path === '/reservations') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['user_id']) || !isset($data['room_id']) || !isset($data['check_in']) || !isset($data['check_out'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit();
    }

    $user_id = intval($data['user_id']);
    $room_id = intval($data['room_id']);
    $check_in = htmlspecialchars($data['check_in']);
    $check_out = htmlspecialchars($data['check_out']);

    $stmt = $conn->prepare("INSERT INTO reservations (user_id, room_id, check_in, check_out) VALUES (:user_id, :room_id, :check_in, :check_out)");
    try {
        $stmt->execute([
            'user_id' => $user_id,
            'room_id' => $room_id,
            'check_in' => $check_in,
            'check_out' => $check_out
        ]);
        http_response_code(201);
        echo json_encode(["message" => "Reservation created successfully"]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(["error" => "Failed to create reservation"]);
    }
    exit();
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
