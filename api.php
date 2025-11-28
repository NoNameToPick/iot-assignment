<?php
header("Content-Type: application/json");

require "config.php";

// Accept both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $temperature = $data['temperature'] ?? null;
    $humidity = $data['humidity'] ?? null;
} else {
    $temperature = $_GET['temperature'] ?? null;
    $humidity = $_GET['humidity'] ?? null;
}

if($temperature !== null && $humidity !== null){
    $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (?, ?)");
    $stmt->bind_param("dd", $temperature, $humidity);

    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Data inserted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert data"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}

$conn->close();
?>

//http://localhost/iot-test/api.php?temperature=26.5&humidity=55