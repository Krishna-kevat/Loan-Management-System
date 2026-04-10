<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../frontend/customer_login.html");
    exit();
}

require_once '../config.php';

// Collect and validate form data
$customer_id = $_SESSION['customer_id'];
$loan_type   = trim($_POST['loan_type']);
$amount      = trim($_POST['amount']);
$tenure      = trim($_POST['tenure']);
$purpose     = trim($_POST['purpose']);
$income      = trim($_POST['income']);

// 🚨 Validation
$errors = [];
if (empty($loan_type))  $errors[] = "Loan type is required.";
if (empty($amount) || !is_numeric($amount) || $amount <= 0) $errors[] = "Enter a valid loan amount.";
if (empty($tenure) || !is_numeric($tenure) || $tenure <= 0) $errors[] = "Enter a valid tenure in months.";
if (empty($purpose))   $errors[] = "Loan purpose is required.";
if (empty($income) || !is_numeric($income) || $income <= 0) $errors[] = "Enter valid income.";

// 📂 File Upload Validation
$upload_dir = "../../uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Required docs (must upload all 5)
$required_docs = ["aadhar", "pan", "salaryslip", "bankstatement", "addressproof"];
$allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];

$uploaded_files = [];
foreach ($required_docs as $doc) {
    if (!isset($_FILES['documents']['name'][$doc]) || $_FILES['documents']['error'][$doc] !== UPLOAD_ERR_OK) {
        $errors[] = ucfirst($doc) . " document is required.";
        continue;
    }

    $document_name = $_FILES['documents']['name'][$doc];
    $document_tmp  = $_FILES['documents']['tmp_name'][$doc];
    $ext = strtolower(pathinfo($document_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_types)) {
        $errors[] = ucfirst($doc) . " must be a PDF, JPG, JPEG, or PNG file.";
        continue;
    }

    $db_save_path  = "uploads/" . time() . "_" . $doc . "." . $ext;
    $physical_path = $upload_dir . time() . "_" . $doc . "." . $ext;

    if (move_uploaded_file($document_tmp, $physical_path)) {
        $uploaded_files[$doc] = $db_save_path;
    } else {
        $errors[] = "Failed to upload " . ucfirst($doc) . ".";
    }
}


// Stop if errors exist
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
    exit();
}

// Save docs JSON
$documents_json = json_encode($uploaded_files);

// Insert into database
$sql = "INSERT INTO loan_application 
(customer_id, loan_type, amount, tenure, purpose, income, document, status, applied_date) 
VALUES 
('$customer_id', '$loan_type', '$amount', '$tenure', '$purpose', '$income', '$documents_json', 'Submitted', NOW())";

header('Content-Type: application/json');
if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Loan Application Submitted Successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>

