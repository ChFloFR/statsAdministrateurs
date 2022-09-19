<?php
$dir = __DIR__ . '/logs/';


//SCANDIR_SORT_DESCENDING liste les fichiers et dossiers dans un dossier
$logsInArray = scandir($dir, SCANDIR_SORT_DESCENDING);

if(!empty($logsInArray)){
    // // Aller chercher et lire le fichier de logs
    foreach($logsInArray as $annee){
        if ($annee === "." || $annee === ".."){
            continue;
        }
        
        //Dans $annee, trouver les mois
        $file_mois = scandir($dir.$annee."/", SCANDIR_SORT_DESCENDING);
        if(!empty($file_mois)){
            
            foreach($file_mois as $mois){
                
                if ($mois === "." || $mois === ".."){
                    continue;
                }

                // les logs journaliers de chaque $mois : $dayLog
                $listLog = scandir($dir.$annee."/".$mois, SCANDIR_SORT_DESCENDING);
                if(!empty($listLog)){ //si tu as trouvé les logs journaliers
                    foreach($listLog as $dayLog){ //pour chaque occurence, il devient $dayLog
                        if($dayLog === "." || $dayLog === ".."){
                            continue; // n'affiche pas les dossiers
                        }
                        file_get_contents($dayLog);
                        var_dump($dayLog);
                        // $allLog = scandir($dir.$annee."/".$mois."/", SCANDIR_SORT_DESCENDING);
                        
                        //--OK , jusque là tout va bien ...($dayLog)
                        // if(!empty($allLog)){
                        //     foreach($allLog as $oneLog){
                                

                        //             // Affiche chaque log dans le bon ordre
                        //             var_dump($oneLog, "<br>");

                        //         // var_dump($oneLog, "<br>"); 
                        //     }
                        //     // var_dump($oneLog, "<br>"); 
                        // }
                        // file_get_contents($oneLog);
                            // lit les 10 fichiers de log

                        }
                        // lit 2 fois les dossiers d'années
                    }
                    // lit 2 fois les dossiers d'années
                    
                }
                // lit 1 fois le dossier année
            }
            // lit 1 fois le dossier année
        }
        // lit 1 fois le dossier année
    }

         
// ne lit plus rien (évidemment ...)

?>