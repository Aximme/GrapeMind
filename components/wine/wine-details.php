<?php
global $conn;
include 'wine-details-2.php';
include __DIR__ . '/../header.php';

$typeClass = '';
if (isset($row['Type'])) {
    switch (strtolower($row['Type'])) {
        case 'red':
            $typeClass = 'frame-red';
            break;
        case 'white':
            $typeClass = 'frame-white';
            break;
        case 'sparkling':
            $typeClass = 'frame-sparkling';
            break;
        case 'rosé':
            $typeClass = 'frame-rose';
            break;
        default:
            $typeClass = 'frame-default';
    }
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cave'])) {
    $idwine = isset($_POST['idwine']) ? $_POST['idwine'] : null;
    $id_user = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $type = $_POST['add_to_cave']; // 'real' ou 'wishlist'

    if ($idwine && $id_user) {
        $checkQuery = $conn->prepare("SELECT * FROM cave WHERE idwine = ? AND id_user = ? AND type = ?");
        $checkQuery->bind_param("iis", $idwine, $id_user, $type);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            $message = "Ce vin est déjà dans votre cave.";
        } else {
            $query = $conn->prepare("INSERT INTO cave (idwine, id_user, type) VALUES (?, ?, ?)");
            $query->bind_param("iis", $idwine, $id_user, $type);
            $query->execute();
            $message = "Vin ajouté avec succès !";
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_grenier'])) {
    $idwine = isset($_POST['idwine']) ? $_POST['idwine'] : null;
    $id_user = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

    if ($idwine && $id_user) {
        $checkCave = $conn->prepare("SELECT * FROM cave WHERE idwine = ? AND id_user = ?");
        $checkCave->bind_param("ii", $idwine, $id_user);
        $checkCave->execute();
        $resultCave = $checkCave->get_result();

        if ($resultCave->num_rows > 0) {
            $message = "Impossible d'ajouter au grenier : ce vin est déjà dans la cave.";
        } else {
            $checkQuery = $conn->prepare("SELECT * FROM grenier WHERE idwine = ? AND id_user = ?");
            $checkQuery->bind_param("ii", $idwine, $id_user);
            $checkQuery->execute();
            $result = $checkQuery->get_result();

            if ($result->num_rows > 0) {
                $message = "Ce vin est déjà dans votre grenier.";
            } else {
                $query = $conn->prepare("INSERT INTO grenier (idwine, id_user) VALUES (?, ?)");
                $query->bind_param("ii", $idwine, $id_user);
                $query->execute();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="stylesheet" href="/GrapeMind/css/main.css"/>
    <link rel="stylesheet" href="/GrapeMind/css/wine-details.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Baloo 2:wght@400;700&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Baloo:wght@400&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aclonica:wght@400&display=swap"/>
    <!--LOADER-->
    <script defer src="/GrapeMind/js/loader.js"></script>
    <style>
        .message {
            color: red;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        .hidden {
            display: none;
        }
        .modal {
            position: fixed;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            position: relative;
        }
        .close {
            cursor: pointer;
            font-size: 24px;
            position: absolute;
            right: 10px;
            top: 10px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("caveModal");
            const closeModal = document.querySelector("#caveModal .close");

            function showCaveOptions() {
                modal.style.display = "flex";
            }

            function closeCaveOptions() {
                modal.style.display = "none";
            }

            document.querySelector(".button-cave").addEventListener("click", function (event) {
                event.preventDefault(); 
                showCaveOptions();
            });

            closeModal.addEventListener("click", function () {
                closeCaveOptions();
            });

            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    closeCaveOptions();
                }
            });
        });
    </script>

</head>
<body>
<?php if (!empty($message)): ?>
    <div id="message" class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="dtails-sur-un-vin">
    <img class="image-detail-vin-icon" alt=""
         src="<?php echo(isset($row['thumb']) ? $row['thumb'] : 'image_detail_vin.png'); ?>">


    <div class="group-parent">
        <div class="acidit-parent">
            <b class="acidit">ACIDITÉ</b>
            <div class="container">
            </div>
            <img class="group-child" alt="" src="/GrapeMind/assets/images/Group%209.png">

            <div class="tooltip">


                <b class="title">  <?php echo(isset($row['Acidity']) ? $row['Acidity'] : ''); ?></b>

                <div class="body-text">
                </div>
            </div>
        </div>
        <div class="degrs-alcool-parent">
            <b class="degrs-alcool">DEGRÈS ALCOOL</b>
            <b class="ABV"><?php echo(isset($row['ABV']) ? $row['ABV'] : ''); ?>°</b>


            <div class="container1">

            </div>
            <div class="group-item">
                <img alt="" src="../../assets/images/Group%209.png">

            </div>


        </div>
        <b class="cepages">Cépages :
            <?php
            $grapes = isset($row['Grapes']) ? $row['Grapes'] : 'Pinot noir';
            $cleaned_grapes = str_replace(array('[', ']', "'"), '', $grapes);
            echo htmlspecialchars($cleaned_grapes);
            ?>
        </b>

        <b class="type">Type : </b>
        <b class="vin-conomique">
            <span>🌱</span>

        </b>
        <b class="composition-100">Composition
            : <?php echo(isset($row['Elaborate']) ? $row['Elaborate'] : '100% variété'); ?></b>

        <div class="frame-child">
        </div>
        <b class="avec-quoi-le-container">
            <p class="sous-titre">Avec quoi le manger?</p>
            <p class="p"></p>
        </b>
        <div class="frame-item <?php echo($typeClass); ?>">
        </div>

        <div class="frame-inner">
        </div>
        <div class="new-vertical-bar"></div>
        <div class="new-horizontal-bar"></div>


        <div class="accord-mets">
            <img class="winners-podium" alt="" src="../../assets/images/winner_podium.png">

            <?php
            if (isset($row['Harmonize'])) {
                $harmonizeArray = explode(',', str_replace(array('[', ']', "'"), '', $row['Harmonize']));
                $item1 = isset($harmonizeArray[0]) ? trim($harmonizeArray[0]) : '';
                if (!empty($item1)) {
                    echo '<img class="plat_1" src="/GrapeMind/assets/images/logos-main2/' . strtolower(str_replace(' ', '_', $item1)) . '.png" alt="' . ($item1) . '">';
                }
            }
            ?>

            <?php
            if (isset($row['Harmonize'])) {
                $harmonizeArray = explode(',', str_replace(array('[', ']', "'"), '', $row['Harmonize']));
                $item1 = isset($harmonizeArray[2]) ? trim($harmonizeArray[2]) : '';
                if (!empty($item1)) {
                    echo '<img class="plat_3" src="/GrapeMind/assets/images/logos-main2/' . strtolower(str_replace(' ', '_', $item1)) . '.png" alt="' . ($item1) . '">';
                }
            }
            ?>

            <?php
            if (isset($row['Harmonize'])) {
                $harmonizeArray = explode(',', str_replace(array('[', ']', "'"), '', $row['Harmonize']));
                $item1 = isset($harmonizeArray[1]) ? trim($harmonizeArray[1]) : '';
                if (!empty($item1)) {
                    echo '<img class="plat_2" src="/GrapeMind/assets/images/logos-main2/' . strtolower(str_replace(' ', '_', $item1)) . '.png" alt="' . ($item1) . '">';
                }
            }
            ?>


            <div class="accord-mets-child">
            </div>
            <div class="accord-mets-item">
            </div>
            <div class="accord-mets-inner">
            </div>
        </div>


        <?php
        if (isset($row['Harmonize'])) {
            $harmonizeArray = explode(',', str_replace(array('[', ']', "'"), '', $row['Harmonize']));
            $item1 = isset($harmonizeArray[0]) ? trim($harmonizeArray[0]) : '';
            $item2 = isset($harmonizeArray[1]) ? trim($harmonizeArray[1]) : '';
            $item3 = isset($harmonizeArray[2]) ? trim($harmonizeArray[2]) : '';
        }
        ?>


        <b class="plats1">
            <?php echo($item1); ?>
        </b>

        <b class="plats2">
            <?php echo($item2); ?>
        </b>

        <b class="plats3">
            <?php echo($item3); ?>
        </b>


        <b class="prix-90">Prix <?php echo(isset($row['price']) ? $row['price'] : '90'); ?> €</b>


        <?php
        if (!empty($row['flavorGroup_1'])) {
            $flavor = trim($row['flavorGroup_1']);
            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
            echo '<img class="icon-flavor1" src="' . ($flavorImagePath) . '" alt="' . ($flavor) . '"> ';
        }


        if (!empty($row['flavorGroup_2'])) {
            $flavor = trim($row['flavorGroup_2']);
            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
            echo '<img class="icon-flavor2" src="' . ($flavorImagePath) . '" alt="' . ($flavor) . '"> ';
        }


        if (!empty($row['flavorGroup_3'])) {
            $flavor = trim($row['flavorGroup_3']);
            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
            echo '<img class="icon-flavor3" src="' . ($flavorImagePath) . '" alt="' . ($flavor) . '"> ';
        }
        ?>


        <div class="armes">ARÔMES</div>
        <div class="flavor-1">
            <?php echo(isset($row['flavorGroup_1']) ? $row['flavorGroup_1'] : ''); ?>
        </div>
        <div class="flavor-2">
            <?php echo(isset($row['flavorGroup_2']) ? $row['flavorGroup_2'] : ''); ?>
        </div>
        <div class="flavor-3">
            <?php echo(isset($row['flavorGroup_3']) ? $row['flavorGroup_3'] : ''); ?>
        </div>


    </div>
    <div class="dtails-sur-un-vin-child">
        <?php
        $rating = isset($row['average_rating']) ? floatval($row['average_rating']) : 0;

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($rating)) {
                echo '<img src="../../assets/images/StarFilled.png" alt="filled star" class="star">';
            } elseif ($i - 0.5 <= $rating) {
                echo '<img src="../../assets/images/StarHalfFilled.png" alt="half-filled star" class="star">';
            } else {
                echo '<img src="../../assets/images/StarOutlineFilled.png" alt="empty star" class="star">';
            }
        }
        ?>
        <span class="average-rating"><?php echo number_format($rating, 1); ?> / 5</span>
    </div>


    <div class="tooltip1">
        <button type="button" class="button-cave" onclick="showCaveOptions()">Ajouter à la cave</button>
    </div>

    <div id="caveModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter à la cave</h2>
            <form method="post" action="">
                <input type="hidden" name="idwine" value="<?php echo $row['idwine']; ?>">
                <button type="submit" name="add_to_cave" value="real">Ajouter à ma cave réelle</button>
                <button type="submit" name="add_to_cave" value="wishlist">Ajouter à ma liste d'envie</button>
            </form>
        </div>
    </div>



    <div class="tooltip2">
        <form method="post" action="">
            <input type="hidden" name="idwine" value="<?php echo $row['idwine']; ?>">
            <button type="submit" name="add_to_grenier" class="button-grenier">Ajouter au grenier</button>
        </form>
    </div>

    <div class="titre-vin">

        <p class="sous-titre">
            <?php echo(isset($row['WineryName']) ? $row['WineryName'] : ''); ?>
        </p>
        <p class="pays-region">
            <span>
                <span>
                    <?php
                    echo (isset($row['Country']) ? $row['Country'] : '') . ', ' .
                        (isset($row['RegionName']) ? $row['RegionName'] : '');
                    ?>
                </span>


            </span>
        </p>

    </div>




    <img class="favorite-icon" alt="" src="../../assets/images/winecavestock-logo.png">

    <img class="logo_grenier" alt="" src="../../assets/images/cave-logo.png">

</div>


<script src="../../js/cursor_acidity.js"></script>
<?php include __DIR__ . '/../footer.php'; ?>

</body>
</html>