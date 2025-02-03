<?php
include 'db_connection.php';

if (isset($_GET['category']) && isset($_GET['content'])) {
    $category = $_GET['category'];
    $content_type = $_GET['content'];


    $column = ($content_type === 'email') ? 'email' : 'phone_number';

    $stmt = $conn->prepare("SELECT date, $column FROM phone_numbers WHERE category = ? AND contact_type = ? ORDER BY date");
    $stmt->bind_param("ss", $category, $content_type);
    $stmt->execute();
    $result = $stmt->get_result();

    $fileContent = '';
    $currentDate = '';
    $contentArray = [];

    while ($row = $result->fetch_assoc()) {
        if ($currentDate !== $row['date']) {
            if (!empty($contentArray)) {
                $fileContent .= implode("\n", $contentArray) . "\n\n";
                $contentArray = [];
            }
            $currentDate = $row['date'];
            $fileContent .= $currentDate . "\n";
        }
        if (!empty($row[$column])) {
            $contentArray[] = $row[$column];
        }
    }

    if (!empty($contentArray)) {
        $fileContent .= implode("\n", $contentArray) . "\n\n";
    }

    if (empty($fileContent)) {
        $fileContent = "No data found for category: $category and content type: $content_type";
    }

    $fileName = $category . "_" . $content_type . ".txt";

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    echo $fileContent;

    $stmt->close();
}

$conn->close();
?>