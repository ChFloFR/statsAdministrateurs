<?php
// ----------------------VARIABLES GLOBALES---------------------------

$dir = __DIR__ . '/logs/';

$arrayUsers = []; // Tableau de l'ensemble des utilisateurs uniques
$forTotalUsers = 0; // Total des utilisateurs uniques trouvés
$forTotalFiles = 0; // Total des fichiers lus
$allLogs = 0; // Nécessaire à l'action du bouton "tout"
$versionOfPropGlobal = []; // Toutes les versions de Propagest utilisées / période demandée
// $averagePropVs = $versionOfPropGlobal/$forTotalUsers; // Moyenne des utilisateurs sur la période
$versionOfOS = []; // Toutes les versions des OS utilisés / période demandée
// $averageOfOs = $versionOfOS/$forTotalUsers;

//&&&&&&&&&&&&& CONFIGURATION DES INTERVALLES DE TEMPS &&&&&&&&&&&&&&&

$interval = "";
$nb_jours = 0;
//si les boutons 30 60 et 90 n'ont pas rempli get limit
if(!empty($_GET['limit']) && in_array($_GET['limit'], [30,60,90])){
    $nb_jours = $_GET['limit'];
    $interval = date("d/m/y", strtotime("today - {$_GET["limit"]}day"));
}else{
    $files = scandir($dir, SCANDIR_SORT_ASCENDING);
    $annee = $files[2];

    $files = scandir("$dir/$annee", SCANDIR_SORT_ASCENDING);
    $mois = $files[2];

    $files = scandir("$dir/$annee/$mois", SCANDIR_SORT_ASCENDING);
    $jour = str_replace('.log', '', $files[2]);

    $interval = "$annee-$mois-$jour"  ;
    
    $nb_jours = $objDateTime;
    
    $objDateTime = new DateTime('NOW');
    $target = new DateTime($interval);
    $intervalBis = $objDateTime->diff($target);
    $nb_jours = abs($intervalBis->format('%R%a days'));

    // var_dump($nb_jours);
    
}
//-----------------------------------------------------STATISTIQUES --------------------------
$averageFilesUsers = 0; // moyenne des fichiers journaliers sur la période demandée
$averageUsersPeriod = 0; // Moyenne d'utilisateurs journaliers sur la période demandée

// ----------------------------------FIN DE CONFIGURATION DES GLOBALES--------------------



//----------------------------LIRE LES DOSSIERS 
$files = scandir($dir, SCANDIR_SORT_DESCENDING);
if (!empty($files)) {

    foreach ($files as $annee) {

        if ($annee === "." || $annee === "..") {
            continue;
        }
        // echo "/" . $annee . "<br>";

        $file_mois = scandir($dir . $annee . "/", SCANDIR_SORT_DESCENDING);
        if (!empty($file_mois)) {
            foreach ($file_mois as $mois) {

                if ($mois === "." || $mois === "..") {
                    continue;
                }
                $listLog = scandir($dir . $annee . "/" . $mois, SCANDIR_SORT_DESCENDING);
                if (!empty($listLog)) {
                    foreach ($listLog as $dayLog) {
                        $nbreUsers = 0; 
                        $readLogs = file_get_contents($dir . $annee . "/" . $mois . "/" . $dayLog);

                        $regexForAll = '/user: (.*?) \/\/ version: (.*?) \/\/ (.+?): (.*?) \/\/ support: (.*?) \/\/ data: (?:(Envoi|Prends) (\d+) .*?|(.*?))$/m';

                        $arrayMatch =[];

                        preg_match_all($regexForAll, $readLogs, $arrayMatch);

                        // ----------------NOMBRE D UTILISATEURS-----------------------------------
                        if(!empty($arrayMatch[1]) ){

                            $arrayMatch[1] = array_unique($arrayMatch[1]);
                            $nameUsers = 0;
                            foreach($arrayMatch[1] as $unikValue => $nameUsers){ 
                                $nbreUsers++;
                                $forTotalUsers++; 
                            }
                        }

                        //----------------------------NBRE DE DOSSIERS-----------------------------
                        
                        if(!empty($arrayMatch[7])){
                            $forTotalFiles = array_sum($arrayMatch[7]); 
                        }
                        //-----------------------------VERSION PROPAGEST -----------------------
                        if(!empty($arrayMatch[2])){

                            foreach($arrayMatch[2] as $version){
                                if(!empty($versionOfPropGlobal[$version])){
                                    $versionOfPropGlobal[$version]++;
                                }else{
                                    $versionOfPropGlobal[$version] = 1;
                                }
                            }
                            $averagePropVs = $totalVersions[1]/$forTotalUsers;
                        }
                        //-----------------------------VERSION ANDROID ----------------------
                        if(!empty($arrayMatch[4])){
                            foreach($arrayMatch[4] as $versionOS){
                                if(!empty($versionOfOS[$versionOS])){
                                    $versionOfOS[$versionOS]++;
                                }else{
                                    $versionOfOS[$versionOS] = 1;
                                }
                            } 
                        }
                        //---------------------------MODE RECHERCHE --------------------------
                        $regexLog = ['/^log initialis. le [0-9]{2}\/[0-9]{2}\/ . [0-9]{2}:[0-9]{2}:[0-9]{2}/im'];
                        $remplacementLog = ["<b>$0</b>"];

                        $newContenuLog = preg_replace($regexLog, $remplacementLog, $contenuLog);

                        if (!empty($_GET['recherche'])) {
                            $arrayNewContenuLog = explode("\n", $newContenuLog);

                            $filterArrayNewContenuLog = array_filter($arrayNewContenuLog, function ($valeur) {
                                return stripos($valeur, $_GET['recherche']) !== false;
                            });
                            if (empty($filterArrayNewContenuLog)) {
                                continue;
                            } else {
                                implode("<br>", $filterArrayNewContenuLog) . "<hr>";
                            }
                        } else {
                            $compteur_global++;
                        }
                    }
                }
            }
        } 
    }
}
// si tu as tout lu et rien trouvé suite à l'incrémentation
if ($compteur_global == 0) {
    echo "Nous ne trouvons pas plus d'associations sur votre demande dans le(s) dossier(s) en référence" . "<br><hr>";
}


$totalVersions = array_sum($versionOfPropGlobal);
foreach($versionOfPropGlobal as $version => &$nb){
        $nb = ($nb*100/$totalVersions);
        $nb = number_format($nb, 2);
        $averagePropVs = $nb; 
}
// % versions
$totalOS = array_sum($versionOfOS);
foreach($versionOfOS as $version => &$nb){
    $nb = ($nb*100/$totalOS);
    $nb = number_format($nb, 2);
}
krsort($versionOfOS);
// echo '<pre>'; print_r($versionOfOS); echo '</pre>';
$averageFilesUsers = number_format($forTotalFiles/$nb_jours,2);
$averageUsersPeriod = number_format($forTotalUsers/$nb_jours, 2);

?>
<!-- AFFICHAGE DE LA PAGE -->
<!DOCTYPE HTML>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Propagest 4</title>
    <link rel="stylesheet" href="assets/style/style.css">
    <!-- <script src="/assets/script.js"></script> -->
</head>

<body>
    <header>
        <h1>Sotrasur</h1>
        <div class="triByPeriod">
            <h4>Tri par période :</h4>
            <a href="?limit=30" class="periodeButton"><button  value="30">30 jours</button></a>
            <a href="?limit=60" class="periodeButton"><button  value="60">60 jours</button></a>
            <a href="?limit=90" class="periodeButton"><button  value="90">90 jours</button></a>
            <a href="?limit=0" class="periodeButton" ><button id="tout" type="reset" value="reset">Tout</button></a>
        </div>
        <div class="contenuGlobal">
            <!-- <p>Recherchez  :</p> -->
           
                <form action="nearlyGood.php" method="GET">

                    <input class="placeholder" placeholder="votre recherche" type="text" name="recherche" size="30">
                    <input class="btn-search" type="submit" value="Rechercher">

                </form>

        </div>
    </header>
    <section class="stats">
        <div class="leftSectionStats">
            <div class="period">
                <p>A partir du <?php echo ($interval) ?></p>
            </div>
            <div class="users">
                <p><?php echo ($averageUsersPeriod) ?> utilisateur(s) / jour</p>
            </div>
            <div class="files">
                <p>&nbsp;<?php echo  ($averageFilesUsers) ?> dossier(s) / jour</p>
            </div>
        </div>
        <div class="rightSectionStats">
            <div class="versions">
                <!-- foreach sur chaque version et en calculer le % -- sortir en ordre descendant dans les versions -->
                <?php foreach ($versionOfPropGlobal as $version => &$nb){?>
                    <p>Version <?php echo $version ?> = &nbsp;<span><?php echo $nb ?> %</span></p>
               <? }?>
            </div>
            <div class="parcAndroid">
                <!-- foreach sur chaque version et en calculer le % -- sortir en ordre descendant dans les versions  -->
                <?php foreach($versionOfOS as $versionAndroid => &$nb){?>
                <p>Android <?php echo $versionAndroid ?> : &nbsp;<span><?php  echo $nb ?> %</span></p>
                <?}?>
            </div>
        </div>
    </section>
    <main>

<div class="boxOfLogs">
    <?php
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
        echo "Nous ne trouvons pas plus d'associations sur votre demande dans le(s) dossier(s) en référence" . "<br><hr>";
    }
    ?>
    ?>
        </div>
    </main>
</body>
</html>