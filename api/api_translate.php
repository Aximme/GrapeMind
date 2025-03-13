<?php
global $conn;
require __DIR__ . '/../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = "0819a1b7-0ac8-4024-8300-90321c4ced88:fx";

$query = $conn->query("SELECT idwine, Harmonize FROM descriptifs WHERE Harmonize_FR IS NULL OR Harmonize_FR = ''");
$wines = $query->fetch_all(MYSQLI_ASSOC);

foreach ($wines as $wine) {
    $idwine = $wine['idwine'];
    $harmonize_en = $wine['Harmonize'];

    if (!empty($harmonize_en)) {
        $url = "https://api-free.deepl.com/v2/translate";
        $data = [
            "auth_key" => $apiKey,
            "text" => $harmonize_en,
            "target_lang" => "FR"
        ];

        $options = [
            "http" => [
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "method" => "POST",
                "content" => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $json = json_decode($response, true);

        if (isset($json["translations"][0]["text"])) {
            $harmonize_fr = $json["translations"][0]["text"];

            $update = $conn->prepare("UPDATE descriptifs SET Harmonize_FR = ? WHERE idwine = ?");
            $update->bind_param("si", $harmonize_fr, $idwine);
            $update->execute();

            echo "Vin ID $idwine traduit : $harmonize_fr <br>";
        } else {
            echo "Erreur de traduction pour ID $idwine<br>";
        }
    }
}
echo "Mise à jour terminée !";
?>

