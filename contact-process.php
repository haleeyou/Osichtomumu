<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars($_POST['fullname']);
    $email = htmlspecialchars($_POST['email']);
    $program = htmlspecialchars($_POST['program']);

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

        if ($file['size'] > $maxFileSize) {
            return "File is too large. Max 2MB allowed.";
        }

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
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

    // Notify admin via email
    $to = "admissions@osichtomumu.edu.ng"; // update with your real cPanel email
    $subject = "New Admission Application - $fullname";
    $message = "New applicant submitted:\n\n";
    $message .= "Name: $fullname\nEmail: $email\nProgram: $program\n\n";
    $message .= "Uploaded Files:\nPassport: $passportPath\nO'Level: $olevelPath\nOther: $othercertPath";

    // Proper headers
    $headers = "From: $fullname <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "✅ Application submitted successfully!";
    } else {
        echo "❌ Error: Unable to send email. Please try again later.";
    }
}
?>
