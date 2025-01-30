<?php
include 'db_connection.php';

$counts = [
    'matchmaker_bulk' => 0,
    'matchmaker_whatsapp' => 0,
    'matchmaker_email' => 0,
    'candidate_bulk' => 0,
    'candidate_whatsapp' => 0,
    'candidate_email' => 0,
    'guardian_bulk' => 0,
    'guardian_whatsapp' => 0,
    'guardian_email' => 0
];

$result = $conn->query("SELECT category, contact_type, COUNT(*) AS count FROM phone_numbers GROUP BY category, contact_type");

while ($row = $result->fetch_assoc()) {
    $key = $row['category'] . '_' . $row['contact_type'];
    $counts[$key] = $row['count'];
}

$result = $conn->query("SELECT category, COUNT(*) AS count FROM phone_numbers WHERE email IS NOT NULL GROUP BY category");

while ($row = $result->fetch_assoc()) {
    $key = $row['category'] . '_email';
    $counts[$key] = $row['count'];
}

$conn->close();
?>