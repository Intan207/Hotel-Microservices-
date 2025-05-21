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

if ($method === 'POST' && $path === '/payments') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['reservation_id']) || !isset($data['amount']) || !isset($data['payment_method'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit();
    }

    $reservation_id = intval($data['reservation_id']);
    $amount = floatval($data['amount']);
    $payment_method = htmlspecialchars($data['payment_method']);

    $stmt = $conn->prepare("INSERT INTO payments (reservation_id, amount, payment_method) VALUES (:reservation_id, :amount, :payment_method)");
    try {
        $stmt->execute([
            'reservation_id' => $reservation_id,
            'amount' => $amount,
            'payment_method' => $payment_method
        ]);
        http_response_code(201);
        echo json_encode(["message" => "Payment processed successfully"]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(["error" => "Failed to process payment"]);
    }
    exit();
}

if ($method === 'GET' && preg_match("#^/payments/(\d+)$#", $path, $matches)) {
    $payment_id = $matches[1];
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id = :id");
    $stmt->execute(['id' => $payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($payment) {
        echo json_encode($payment);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Payment not found"]);
    }
    exit();
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
