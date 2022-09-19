<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Propagest 4</title>
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="/assets/script.js"></script>
</head>

<body>
    <header>
        <h1>Sotrasur</h1>
        <div class="triByPeriod">
            <h4>Tri par période :</h4>
            <a href="?limit=30" class="periodeButton"><button class="periodeButton" value="30">30 jours</button></a>
            <a href="?limit=60" class="periodeButton"><button class="periodeButton" value="60">60 jours</button></a>
            <a href="?limit=90" class="periodeButton"><button class="periodeButton" value="90">90 jours</button></a>
        </div>
        <div class="contenuGlobal">
            <!-- <p>Recherchez  :</p> -->
           
                <form action="testStrpos.php" method="GET">

                    <input class="placeholder" type="text" name="recherche" size="30">
                    <input class="btn-search" type="submit" value="Rechercher">

                </form>
            
            <div>
                <!-- peut-être, n'afficher ce bouton que si la recherche n'a rien donné SINON après n'importe quelle recherche éffectuée -->
                <!-- <button type="echo" method_exists="GET">Retour à la liste</button> -->
            </div>
        </div>
    </header>
    <section class="stats">
        <div class="leftSectionStats">
            <div class="users">
                <p>{nbreUsers} utilisateurs / jour</p>
            </div>
            <div class="files">
                <p>{nbreFiles} dossiers / jour</p>
            </div>
        </div>
        <div class="rightSectionStats">
            <div class="versions">
                <!-- foreach sur chaque version et en calculer le % -- sortir en ordre descendant les données -->
                <p>{versionPropagest} : {prcentUsers} %</p>
                <p>{versionPropagest} : {prcentUsers} %</p>
                <p>{versionPropagest} : {prcentUsers} %</p>
            </div>
            <div class="parcAndroid">
                <!-- foreach sur chaque version et en calculer le % -- sortir en ordre descendant les données -->
                <p>{versionAndroid} : {prcentTools} %</p>
                <p>{versionAndroid} : {prcentTools} %</p>
                <p>{versionAndroid} : {prcentTools} %</p>
            </div>
        </div>
    </section>
    <main>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function(){
        // apres initali
    })
</script> -->

<div class="boxOfLogs">
<?php

$compteur_global = 0;
$arrayUsers = []; // Tableau de l'ensemble des utilisateurs uniques
$forTotalUsers = 0; // Total des utilisateurs uniques trouvés
$forTotalFiles = 0; // Total des fichiers lus
$versionOfPropGlobal = []; // Toutes les versions de Propagest utilisées / période demandée
$versionOfOS = []; // Toutes les versions des OS utilisés / période demandée


$dir = __DIR__ . '/logs/';
$files = scandir($dir, SCANDIR_SORT_DESCENDING);
if (!empty($files)) {

    foreach ($files as $annee) {

        if ($annee === "." || $annee === "..") {
            continue;
        }
        echo "/" . $annee . "<br>";

        $file_mois = scandir($dir . $annee . "/", SCANDIR_SORT_DESCENDING);
        if (!empty($file_mois)) {
            foreach ($file_mois as $mois) {

                if ($mois === "." || $mois === "..") {
                    continue;
                }
                echo "|\t\t&nbsp;&nbsp;&nbsp;&nbsp;\t\t&nbsp;&nbsp;&nbsp;&nbsp;" . "/" . $mois . "<br><br>";


                $listLog = scandir($dir . $annee . "/" . $mois, SCANDIR_SORT_DESCENDING);
                if (!empty($listLog)) {
                    foreach ($listLog as $dayLog) {

                        $contenuLog = file_get_contents($dir . $annee . "/" . $mois . "/" . $dayLog);

                        $regexLog = ['/^log initialis. le [0-9]{2}\/[0-9]{2}\/ . [0-9]{2}:[0-9]{2}:[0-9]{2}/im'];
                        $remplacementLog = ["<b>$0</b>"];

                        $newContenuLog = preg_replace($regexLog, $remplacementLog, $contenuLog);
                        // une variable $valide à true, pour afficher l'ensemble des logs -- basculant à false si une recherche est éffectuée

                        if (!empty($_GET['recherche'])) {
                            //si $_GET n'est pas vide, on met tous les logs dans un $arrayNew...
                            $arrayNewContenuLog = explode("\n", $newContenuLog);

                            //ce dernier tableau sera filtré est mis dans $filterArray...
                            $filterArrayNewContenuLog = array_filter($arrayNewContenuLog, function ($valeur) {
                                //strripos pour son insensibilité à la casse, retourne la $valeur trouvé dans $_GET si il est différent de faux (donc qqchose est trouvé)
                                return stripos($valeur, $_GET['recherche']) !== false;
                            });

                            //si pas de match sur la recherche, $valide devient false et affiche un message 
                            if (empty($filterArrayNewContenuLog)) {
                                //continue évitera l'affichage multiple du message
                                continue;
                                //sinon affiche ce qui suit :
                            } else {
                                //affiche l'entête du log
                                echo str_repeat("|&nbsp;&nbsp;&nbsp;&nbsp;", 4) . $arrayNewContenuLog[0] . "<br><br>";
                                //affiche les lignes correspondantes
                                echo implode("<br>", $filterArrayNewContenuLog) . "<hr>";
                            }
                            //sinon --- DONC SI PAS DE RECHERCHE --incrémente et affiche les logs
                        } else {
                            // la var $valide restant à true permet d'afficher l'ensemble des logs 

                            $compteur_global++;
                            //répètes la string "espaces" 4 fois - puis le contenu // pour mise en page
                            echo str_repeat("|&nbsp;&nbsp;&nbsp;&nbsp;", 4) . "&nbsp;&nbsp;&nbsp;&nbsp;" . utf8_encode(nl2br($newContenuLog)) . "<br><br>";
                        }
                    }
                };
            }
        }
    }
}
// si tu as tout lu et rien trouvé suite à l'incrémentation
if ($compteur_global == 0) {
    echo "Nous ne trouvons pas les associations que vous demandez dans le(s) dossier(s) en référence" . "<br><hr>";
}

?>
        </div>
    </main>
<!-- <script src="/assets/script.js"></script> -->
</body>
</html>