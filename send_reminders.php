<?php
require 'db.php';
require 'vendor/autoload.php'; //  Pas necessaire pour l'instant 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Conceptualisation d'envoie de mail automatique A faire pour plus tard 
    $today = date('Y-m-d'); 
    $stmt = $conn->prepare("
        SELECT er.id AS reminder_id, er.reminder_date, er.event_title, er.event_id, er.email_sent, 
               u.email AS user_email, e.name AS event_name
        FROM event_reminders er
        JOIN users u ON er.user_id = u.id
        JOIN events e ON er.event_id = e.id
        WHERE er.reminder_date = ? AND er.email_sent = 0
    ");
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $reminders = $stmt->get_result();

    if ($reminders->num_rows > 0) {
        while ($reminder = $reminders->fetch_assoc()) {
            $user_email = $reminder['user_email']; // Email du destinataire
            $event_title = $reminder['event_name']; // Titre de l'événement
            $reminder_id = $reminder['reminder_id']; // ID du rappel

            // Créer une instance de PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'votre-email@gmail.com'; // Remplacez par votre email SMTP
            $mail->Password = 'votre-mot-de-passe'; // Mot de passe SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurer l'email
            $mail->setFrom('noreply@votre-site.com', 'GrapeMind'); // Adresse de l'expéditeur
            $mail->addAddress($user_email); // Adresse du destinataire
            $mail->Subject = "Rappel pour l'événement : $event_title";
            $mail->Body = "Bonjour,\n\nCeci est un rappel pour l'événement : \"$event_title\" prévu pour aujourd'hui.\n\nCordialement,\nL'équipe GrapeMind.";

            // Envoyer l'email
            if ($mail->send()) {
                // Marquer l'email comme envoyé dans la base de données
                $stmt_update = $conn->prepare("UPDATE event_reminders SET email_sent = 1 WHERE id = ?");
                $stmt_update->bind_param('i', $reminder_id);
                $stmt_update->execute();
            }
        }
        echo "Les rappels ont été envoyés avec succès.\n";
    } else {
        echo "Aucun rappel à envoyer aujourd'hui.\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de l'envoi des emails : {$e->getMessage()}\n";
} catch (\Throwable $th) {
    echo "Erreur système : {$th->getMessage()}\n";
}

