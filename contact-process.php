<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If admission form (has program field)
    if (isset($_POST['program'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $program = $_POST['program'];

        // File upload directory
        $uploadDir = "uploads/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $maxFileSize = 2 * 1024 * 1024;

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

        // Validate and move files
        $passportCheck = validateFile($_FILES["passport"], ["jpg", "jpeg", "png"], $maxFileSize);
        if ($passportCheck !== true) die("Passport Error: " . $passportCheck);

        $olevelCheck = validateFile($_FILES["olevel"], ["pdf"], $maxFileSize);
        if ($olevelCheck !== true) die("O'Level Certificate Error: " . $olevelCheck);

        $passportPath = $uploadDir . uniqid("passport_") . "." . strtolower(pathinfo($_FILES["passport"]["name"], PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["passport"]["tmp_name"], $passportPath);

        $olevelPath = $uploadDir . uniqid("olevel_") . ".pdf";
        move_uploaded_file($_FILES["olevel"]["tmp_name"], $olevelPath);

        $othercertPath = "";
        if (!empty($_FILES["othercert"]["name"])) {
            $othercertCheck = validateFile($_FILES["othercert"], ["pdf"], $maxFileSize);
            if ($othercertCheck !== true) die("Other Certificate Error: " . $othercertCheck);

            $othercertPath = $uploadDir . uniqid("other_") . ".pdf";
            move_uploaded_file($_FILES["othercert"]["tmp_name"], $othercertPath);
        }

        // Notify admin for admission
        $to = "admissions@osichtomumu.edu.ng";
        $subject = "New Admission Application - $fullname";
        $message = "New applicant submitted:\n\nName: $fullname\nEmail: $email\nProgram: $program\n\nFiles:\nPassport: $passportPath\nO'Level: $olevelPath\nOther: $othercertPath";
        @mail($to, $subject, $message);

        echo "✅ Application submitted successfully!";
    }

    // If contact form (no program field)
    elseif (isset($_POST['subject']) && isset($_POST['message'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // Notify admin for contact
        $to = "info@osichtomumu.edu.ng";
        $mailSubject = "Contact Form Submission - $subject";
        $mailBody = "You have a new message from:\n\nName: $fullname\nEmail: $email\n\nMessage:\n$message";
        @mail($to, $mailSubject, $mailBody);

        echo "✅ Your message has been sent successfully!";
    }
}
?>
