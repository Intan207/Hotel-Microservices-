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

if ($method === 'GET' && $path === '/rooms') {
    $stmt = $conn->prepare("SELECT * FROM rooms");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rooms);
    exit();
}

if ($method === 'POST' && $path === '/rooms') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['room_number']) || !isset($data['type']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing room_number, type or price"]);
        exit();
    }

    $room_number = htmlspecialchars($data['room_number']);
    $type = htmlspecialchars($data['type']);
    $price = floatval($data['price']);

    $stmt = $conn->prepare("INSERT INTO rooms (room_number, type, price) VALUES (:room_number, :type, :price)");
    try {
        $stmt->execute(['room_number' => $room_number, 'type' => $type, 'price' => $price]);
        http_response_code(201);
        echo json_encode(["message" => "Room added successfully"]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(["error" => "Failed to add room"]);
    }
    exit();
}

if ($method === 'DELETE' && preg_match("#^/rooms/(\d+)$#", $path, $matches)) {
    $room_id = $matches[1];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = :id");
    if ($stmt->execute(['id' => $room_id])) {
        echo json_encode(["message" => "Room deleted successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Failed to delete room"]);
    }
    exit();
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
