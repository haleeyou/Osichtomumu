<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "info@osichtomumu.edu.ng";
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    if ( empty($name) || empty($subject) || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        echo "Please fill in all fields correctly.";
        exit;
    }

    $email_subject = "Contact Form: $subject";
    $email_body = "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Message:\n$message\n";

    $headers = "From: $name <$email>";

    if (mail($to, $email_subject, $email_body, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Sorry, something went wrong. Please try again later.";
    }
} else {
    echo "Invalid request.";
}
?>
