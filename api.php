<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// TEMP debug (remove later)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "error" => $conn->connect_error,
        "host" => $host
    ]));
}

// TEST CONNECTION
// echo json_encode(["status" => "Connected"]); exit;

// GET all users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $result = $conn->query("SELECT * FROM users");

    if (!$result) {
        die(json_encode([
            "error" => $conn->error
        ]));
    }

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}

// CREATE user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    $name = $data->name ?? '';
    $email = $data->email ?? '';

    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

    if (!$conn->query($sql)) {
        die(json_encode([
            "error" => $conn->error
        ]));
    }

    echo json_encode(["message" => "User added"]);
}
