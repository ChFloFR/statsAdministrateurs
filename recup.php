<?php

$dir = __DIR__ . '/logs/';
//scan du dossier log dans la var $files
$files = scandir($dir);


if(!empty($files)){
    //chaque dossier est $annee
    foreach($files as $annee){

        //ne renvoit pas " . " & " .. "
        if ($annee === "." || $annee === ".."){
            continue;
        }
        //Dans $annee, trouver les mois
        $file_mois = scandir($dir.$annee."/");
        if(!empty($file_mois)){
            foreach($file_mois as $mois){


                if ($mois === "." || $mois === ".."){
                    continue;
                }
            //même démarche pour les logs journaliers de chaque $mois : $dayLog
                $listLog = scandir($dir.$annee."/".$mois);
                if(!empty($listLog)){
                    foreach($listLog as $dayLog){
                        $contenuLog = file_get_contents($dir.$annee."/".$mois."/".$dayLog);
                        //regexLog a pour usage de trouver la ligne qui contient les caractères soumis
                        $regexLog = '/^log initialis. le [0-9]{2}\/[0-9]{2}\/ . [0-9]{2}:[0-9]{2}:[0-9]{2}/im';
                        // table vide pour réception
                        $match= [];
                        //const preg a pour paramètres: la const regex, celle du contenu et la table vide
                        $preg_match = preg_match($regexLog, $contenuLog, $match);
                        //si le match fonctionne, 
                        if($preg_match == true){
                            //$replacementLog sera écrite telle que :
                            $replacementLog = "<b>" . $match[0] . "</b>";
                            
                            $newContenuLog = str_replace($match[0], $replacementLog, $contenuLog);
                            echo "<pre>"; var_dump($newContenuLog); echo "</pre>";
                            file_put_contents($dayLog, $newContenuLog);
                        }
                        // écriture sur $dayLog par les valeurs de $newContenuLog /
                    }
                }                
            }
        }
 

    }
}

?>

