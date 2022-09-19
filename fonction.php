<?php
//il me faut le calcul d'un 1 mois

//pour 90 j, additionner les variables contenant 3 mois différents donc une variable par mois (fonction prenant la date pour l'incrémenter par exemple ?) CREATION DU NOM DE VARIABLE AUTOMATIQUE ?

                //genre fonctionRecupDeDate(avec je ne sais pas quelle fonction){
                //ce que tu récupères, tu déclares une $ceQueJeRécupère


//}


// ----------------------VARIABLES GLOBALES---------------------------


$arrayUsers = []; // Tableau de l'ensemble des utilisateurs uniques
$forTotalUsers = 0; // Total des utilisateurs uniques trouvés
$forTotalFiles = 0; // Total des fichiers lus
$versionOfPropGlobal = []; // Toutes les versions de Propagest utilisées / période demandée
$versionOfOS = []; // Toutes les versions des OS utilisés / période demandée
// $averageFilesUsers = $forTotalFiles/$forTotalUsers;

// ----------------------FIN DE CONFIGURATION DES GLOBALES-------

//recup de la date, et moins 30 jours
        //date formate une date/heure locale - ici jour en cours
$today = date("d/m/y") . "<br>";
        // strtotime prend deux paramètes - une chaine date/heure et le timestamp (date du jour)
        //création des dates pour les 3 boutons en tant que variables globales -
$oneMonthAgo = date("d/m/y", strtotime("today - 30day"));
$twoMonthAgo = date("d/m/y", strtotime("today - 60day"));
$threeMonthAgo = date("d/m/y", strtotime("today - 90day"));

//récupérer la valeur du bouton cliqué // javascript ?
//le bouton lance la fonction créée avec le paramètre chiffré (30. 60 90)
$clicForSortbyDate = date("d/m/y", strtotime(""));



//----------------------------LIRE LES DOSSIERS ET FICHIERS
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
                        $nbreUsers = 0; 
                        $readLogs = file_get_contents($dir . $annee . "/" . $mois . "/" . $dayLog);
                        //Lis $dayLog et cherche $regexName dans $daylog = $resultatUser
                        //$regexAll récupère la string pour créer l'utilisateur PUIS la version du logiciel PUIS l'OS PUIS la version de l'OS PUIS le type de support PUIS OU la chaine de caractère(connexion) OU le nombre de dossiers pris ou envoyés -- 
                        $regexForAll = '/user: (.*?) \/\/ version: (.*?) \/\/ (.+?): (.*?) \/\/ support: (.*?) \/\/ data: (?:(Envoi|Prends) (\d+) .*?|(.*?))$/m';
                        //puis renvoit la string en question
                        // $resultatUser = ["$0"];
                        $arrayMatch =[];
                        //ranges la dans un tableau $arrayUsers[];
                        // if( !empty($arrayUsers[$resultatUser]) )
                        preg_match_all($regexForAll,$readLogs, $arrayMatch);
                        //si le résultatUser n'existe pas - strtolower pour s'échapper de la casse à intégrer ?

                        // ----------------NOMBRE D UTILISATEURS
                        if(!empty($arrayMatch[1]) ){
                            // array_unique() extrait les valeurs distinctes et supprime les doublons
                            $arrayMatch[1] = array_unique($arrayMatch[1]);
                            //parcourir le tableau
                            $nameUsers = 0;
                            //chaque valeur trouvée devient unique et incrémente le compteur journalier
                            foreach($arrayMatch[1] as $unikValue => $nameUsers){ 
                                $nbreUsers++;
                                $forTotalUsers++; 
                            }
                                // Les totaux journaliers ici
                            echo "le " . "$dayLog" . " = " . "$nbreUsers" . " utilisateur(s)<br>";
                        }
                        // echo "soit " . ($forTotalUsers/$forTotalFiles) . "dossier(s) chaque jour<br>";
                        //---------------------NBRE DE DOSSIERS
                        echo '<pre>'; print_r($arrayMatch); echo '</pre>';
                        
                        if(!empty($arrayMatch[7])){
                            $forTotalFiles = array_sum($arrayMatch[7]); 
                            echo "le " . "$dayLog" . " = " . "$forTotalFiles" . " fichier(s)<br>" . number_format($forTotalFiles/ $forTotalUsers, 2) . " dossiers/personne" ."<hr>"; //CHANGER $ forTotalUsers POUR LE NOMBRE DE JOURS
                        }

                        //------------VERSION PROPAGEST ------------------
                        if(!empty($arrayMatch[2])){
                            // compter chaque fois qu'une version est trouvée
                            //Chaque occurence du 2e index du tableau, en tant que version unique, est rangée dans le tableau (global)
                            foreach($arrayMatch[2] as $version){
                                //si l'index 2 du tableau n'est pas vide, dans ma globale, l'index $version
                                if(!empty($versionOfPropGlobal[$version])){
                                    //chaque lecture d'une version s'incrémente de 1
                                    $versionOfPropGlobal[$version]++;
                                }else{
                                    //si toutefois la version n'existe pas, je la crée et l'incrémente de 1
                                    $versionOfPropGlobal[$version] = 1;
                                }
                            }
                        }
                        //-----------VERSION ANDROID ----------------------
                        if(!empty($arrayMatch[4])){
                            foreach($arrayMatch[4] as $versionOS){
                                if(!empty($versionOfOS[$versionOS])){
                                    $versionOfOS[$versionOS]++;
                                }else{
                                    $versionOfOS[$versionOS] = 1;
                                }
                            } 
                        }
                    }

                }
            }
            echo "total des utilisateurs " . "$forTotalUsers<br>";
        }
    }
}



// Créé le total de toutes les versions issues du tableau $versionOfPropGlobal
$totalVersions = array_sum($versionOfPropGlobal);
//pour chaque occurrence lue, telle une version, attribues la valeur $nb par passage de référence (soit sa valeur ici)
foreach($versionOfPropGlobal as $version => &$nb){
    //donc sa valeur est, ici, égale à sa valeur en %, ce qu'il va renvoyer
        $nb = ($nb*100/$totalVersions);
        // sortie avec 2 décimales
        $nb = number_format($nb, 2);
}
echo '<pre>'; print_r($versionOfPropGlobal); echo '</pre>';

$totalOS = array_sum($versionOfOS);
foreach($versionOfOS as $version => &$nb){
    $nb = ($nb*100/$totalOS);
    $nb = number_format($nb, 2);

}
krsort($versionOfOS);
echo '<pre>'; print_r($versionOfOS); echo '</pre>';

// total de chaque version x100 / $total

// Entre les dates concernées : Ranger les users dans un tableau et les identifier sauf si déjà vu

//variable globale nbre d'utilisateurs
// function allUsersPeriod($unMois, $deuxMois, $troisMois){
//     //fonction pour choisir automatique la variable de jours / mois ?????
//     // $forTotalUsers/$oneMonthAgo = $result;
    
//         if($oneMonthAgo){
//             $result = $unMois;
//             return $result;
//         }if($twoMonthAgo){
//             $result = $unMois + $deuxMois;
//         }else($threeMonthAgo){
//             $result = $unMois + $deuxMois + $troisMois
//         }
//     }


// function howManyUsers($oneMonthAgo){
//     // if
//     //si clic sur 30j use $oneMonthAgo PARAMETRE ADAPTABLE ?
//     // if
//     //si clic sur 60j use $twoMonthAgo PARAMETRE ADAPTABLE ?
//     //else
//     //si clic sur 90j use $threeMonthAgo PARAMETRE ADAPTABLE ?


// }

?>
