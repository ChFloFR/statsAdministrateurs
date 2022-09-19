<?php

$dir = __DIR__ . '/logs/';

$files = scandir($dir, SCANDIR_SORT_DESCENDING);


if(!empty($files)){

    foreach($files as $annee){

        if ($annee === "." || $annee === ".."){
            continue;
        }


        $file_mois = scandir($dir.$annee."/", SCANDIR_SORT_DESCENDING);
        if(!empty($file_mois)){
            foreach($file_mois as $mois){


                if ($mois === "." || $mois === ".."){
                    continue;
                }
                
                $listLog = scandir($dir.$annee."/".$mois, SCANDIR_SORT_DESCENDING);
                if(!empty($listLog)){
                    foreach($listLog as $dayLog){
                        $contenuLog = file_get_contents($dir.$annee."/".$mois."/".$dayLog);

                        $regexLog = ['/^log initialis. le [0-9]{2}\/[0-9]{2}\/ . [0-9]{2}:[0-9]{2}:[0-9]{2}/im'];
                        $remplacementLog = ["<b>$0</b>"  ];
                        // var_dump($remplacementLog);

                        $newContenuLog = preg_replace($regexLog, $remplacementLog, $contenuLog);     
                        if($newContenuLog){
                            file_put_contents($dir.$annee."/".$mois."/".$dayLog, $newContenuLog);
                            var_dump($newContenuLog, "<br>");
                        }
                    }
                    

                }                
            }
        }
    }
        
    }
    
?>