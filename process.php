<?php
include 'db_connection.php';

function isValidPhoneNumber($number)
{
    return preg_match("/^\+880(17|18|16|19|13|15|14)\d{8}$/", $number);
}
 
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $contactType = $_POST['contactType'];
    $inputType = $_POST['inputType'];
    $error = '';
    $success = '';
    $currentDate = date('Y-m-d');

    if (empty($category) || empty($contactType)) {
        $error = "Please select a category and contact type.";
    } elseif ($inputType === 'file' && isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['fileInput']['tmp_name']);
        if ($fileType !== 'text/plain') {
            $error = "Please upload a valid text file.";
        } else {
            $file = $_FILES['fileInput']['tmp_name'];
            $handle = fopen($file, "r");
            while (($line = fgets($handle)) !== false) {
                $contact = trim($line);
                if (isValidPhoneNumber($contact)) {
                    $stmt = $conn->prepare("SELECT * FROM phone_numbers WHERE phone_number = ?");
                    $stmt->bind_param("s", $contact);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows == 0) {
                        $stmt = $conn->prepare("INSERT INTO phone_numbers (category, contact_type, phone_number, date) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $category, $contactType, $contact, $currentDate);
                        $stmt->execute();
                    }
                } elseif (isValidEmail($contact)) {
                    $stmt = $conn->prepare("SELECT * FROM phone_numbers WHERE email = ?");
                    $stmt->bind_param("s", $contact);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $stmt = $conn->prepare("DELETE FROM phone_numbers WHERE email = ?");
                        $stmt->bind_param("s", $contact);
                        $stmt->execute();
                    }
                    $stmt = $conn->prepare("INSERT INTO phone_numbers (category, contact_type, email, date) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $category, $contactType, $contact, $currentDate);
                    $stmt->execute();
                } else {
                    $error = "Invalid contact in file: $contact";
                    break;
                }
            }
            fclose($handle);
            if (empty($error)) {
                $success = "Contacts from the file were added successfully.";
            }
        }
    } elseif ($inputType === 'phone' && isset($_POST['phoneNumber'])) {
        $phoneNumber = trim($_POST['phoneNumber']);
        if (empty($phoneNumber)) {
            $error = "Please enter a phone number.";
        } elseif (!isValidPhoneNumber($phoneNumber)) {
            $error = "Invalid phone number. Must start with +880 and contain 14 digits.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM phone_numbers WHERE phone_number = ?");
            $stmt->bind_param("s", $phoneNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "This phone number already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO phone_numbers (category, contact_type, phone_number, date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $category, $contactType, $phoneNumber, $currentDate);
                if ($stmt->execute()) {
                    $success = "Phone number added successfully.";
                } else {
                    $error = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    } elseif ($inputType === 'email' && isset($_FILES['emailFileInput']) && $_FILES['emailFileInput']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['emailFileInput']['tmp_name']);
        if ($fileType !== 'text/plain') {
            $error = "Please upload a valid text file.";
        } else {
            $file = $_FILES['emailFileInput']['tmp_name'];
            $handle = fopen($file, "r");
            while (($line = fgets($handle)) !== false) {
                $email = trim($line);
                if (isValidEmail($email)) {
                    $stmt = $conn->prepare("SELECT * FROM phone_numbers WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $stmt = $conn->prepare("DELETE FROM phone_numbers WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                    }
                    $stmt = $conn->prepare("INSERT INTO phone_numbers (category, contact_type, email, date) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $category, $contactType, $email, $currentDate);
                    $stmt->execute();
                } else {
                    $error = "Invalid email in file: $email";
                    break;
                }
            }
            fclose($handle);
            if (empty($error)) {
                $success = "Emails from the file were added successfully.";
            }
        }
    } else {
        $error = "Please choose a phone number or file to upload.";
    }
    if (!empty($error)) {
        header("Location: index.php?error=" . urlencode($error));
    } else {
        header("Location: index.php?success=" . urlencode($success));
    }
    exit();
}
?>