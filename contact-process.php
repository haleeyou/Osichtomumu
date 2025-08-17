<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $program = $_POST['program'];

    // File upload directory
    $uploadDir = "uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Max file size (2MB)
    $maxFileSize = 2 * 1024 * 1024;

    // Function to validate file
    function validateFile($file, $allowedTypes, $maxFileSize) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "File upload error.";
        }

        // Check file size
        if ($file['size'] > $maxFileSize) {
            return "File is too large. Max 2MB allowed.";
        }

        // Get file extension
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validate extension
        if (!in_array($fileExt, $allowedTypes)) {
            return "Invalid file type. Allowed types: " . implode(", ", $allowedTypes);
        }

        return true;
    }

    // Validate Passport (JPG/PNG)
    $passportCheck = validateFile($_FILES["passport"], ["jpg", "jpeg", "png"], $maxFileSize);
    if ($passportCheck !== true) {
        die("Passport Error: " . $passportCheck);
    }

    // Validate O'Level (PDF only)
    $olevelCheck = validateFile($_FILES["olevel"], ["pdf"], $maxFileSize);
    if ($olevelCheck !== true) {
        die("O'Level Certificate Error: " . $olevelCheck);
    }

    // Validate Other Certificate (optional, PDF only if uploaded)
    if (!empty($_FILES["othercert"]["name"])) {
        $othercertCheck = validateFile($_FILES["othercert"], ["pdf"], $maxFileSize);
        if ($othercertCheck !== true) {
            die("Other Certificate Error: " . $othercertCheck);
        }
    }

    // Upload files
    $passportPath = $uploadDir . uniqid("passport_") . "." . strtolower(pathinfo($_FILES["passport"]["name"], PATHINFO_EXTENSION));
    move_uploaded_file($_FILES["passport"]["tmp_name"], $passportPath);

    $olevelPath = $uploadDir . uniqid("olevel_") . ".pdf";
    move_uploaded_file($_FILES["olevel"]["tmp_name"], $olevelPath);

    $othercertPath = "";
    if (!empty($_FILES["othercert"]["name"])) {
        $othercertPath = $uploadDir . uniqid("other_") . ".pdf";
        move_uploaded_file($_FILES["othercert"]["tmp_name"], $othercertPath);
    }

    // Notify admin (optional)
    $to = "admissions@yourdomain.com";
    $subject = "New Admission Application - $fullname";
    $message = "New applicant submitted:\n\nName: $fullname\nEmail: $email\nProgram: $program\n\nUploaded Files:\nPassport: $passportPath\nO'Level: $olevelPath\nOther: $othercertPath";
    @mail($to, $subject, $message);

    echo "✅ Application submitted successfully!";
}
?>
