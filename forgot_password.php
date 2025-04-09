<?php
global $conn;
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expire = date("Y-m-d H:i:s", strtotime("+2 hour"));


        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expire, $email);
        $stmt->execute();


        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@grapemind.fr';
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('noreply@grapemind.fr', 'GrapeMind Support');
            $mail->addAddress($email);
            $mail->Subject = "Réinitialisation de votre mot de passe";
            $mail->Body = "Cliquez ici pour réinitialiser votre mot de passe : http://51.210.243.151:8888/reset_password.php?token=" . $token;

            $mail->send();
            echo "✅ Un email a été envoyé avec un lien de réinitialisation.";
        } catch (Exception $e) {
            echo "❌ Erreur d'envoi : {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Aucun compte associé à cet email.";
    }
}
?>

<form method="post">
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">Envoyer</button>
</form>
