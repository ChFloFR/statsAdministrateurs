
<!DOCTYPE HTML>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <title>Propagest 4</title>
        <style src="/style.css"></style>
        </head>
        
        <body>
            <header>
                <h2>Sotrasur</h2>
            </header>
            <main>

            <div class="contenuGlobal" >
            <h5>Recherchez ce que vous voulez :</h5>
                <div>
                <form action="testStrpos.php" method="GET">
                    Votre recherche <input type="text" name="recherche"> 
                    <input type="submit" value="Rechercher">
                    </form>
                </div>
                <div>
                    <!-- peut-être, n'afficher ce bouton que si la recherche n'a rien donné SINON après n'importe quelle recherche éffectuée -->
                    <!-- <button type="echo" method_exists="GET">Retour à la liste</button> -->
                </div>
            </div>
     </main>
</body>

</html>

<?php


$compteur_global = 0;

$dir = __DIR__ . '/logs/';
$files = scandir($dir, SCANDIR_SORT_DESCENDING);
if(!empty($files)){
    
    foreach($files as $annee){
        
        if ($annee === "." || $annee === ".."){
            continue;
        }
        echo "/".$annee."<br>";
        
        $file_mois = scandir($dir.$annee."/", SCANDIR_SORT_DESCENDING);
        if(!empty($file_mois)){
            foreach($file_mois as $mois){
                
                if ($mois === "." || $mois === ".."){
                    continue;
                }
                echo "|\t\t&nbsp;&nbsp;&nbsp;&nbsp;\t\t&nbsp;&nbsp;&nbsp;&nbsp;"."/".$mois."<br><br>";
                

                $listLog = scandir($dir.$annee."/".$mois, SCANDIR_SORT_DESCENDING);
                if(!empty($listLog)){
                    foreach($listLog as $dayLog){

                        $contenuLog = file_get_contents($dir.$annee."/".$mois."/".$dayLog);

                        $regexLog = ['/^log initialis. le [0-9]{2}\/[0-9]{2}\/ . [0-9]{2}:[0-9]{2}:[0-9]{2}/im'];
                        $remplacementLog = ["<b>$0</b>"];

                        $newContenuLog = preg_replace($regexLog, $remplacementLog, $contenuLog);
                        // une variable $valide à true, pour afficher l'ensemble des logs -- basculant à false si une recherche est éffectuée

                        if (!empty($_GET['recherche'])){
                            //si $_GET n'est pas vide, on met tous les logs dans un $arrayNew...
                            $arrayNewContenuLog = explode("\n",$newContenuLog);

                            //ce dernier tableau sera filtré est mis dans $filterArray...
                            $filterArrayNewContenuLog = array_filter($arrayNewContenuLog, function($valeur){
                                //strripos pour son insensibilité à la casse, retourne la $valeur trouvé dans $_GET si il est différent de faux (donc qqchose est trouvé)
                                return stripos($valeur, $_GET['recherche']) !== false;
                            });  
                            
                            //si pas de match sur la recherche, $valide devient false et affiche un message 
                            if(empty($filterArrayNewContenuLog)){
                                //continue évitera l'affichage multiple du message
                                continue;
                                //sinon affiche ce qui suit :
                            }else{
                                //affiche l'entête du log
                                echo str_repeat("|&nbsp;&nbsp;&nbsp;&nbsp;", 4).$arrayNewContenuLog[0]."<br><br>";
                                //affiche les lignes correspondantes
                                echo implode("<br>",$filterArrayNewContenuLog)."<hr>";
                            }
                            //sinon --- DONC SI PAS DE RECHERCHE --incrémente et affiche les logs
                        }else{
                            // la var $valide restant à true permet d'afficher l'ensemble des logs 
                                
                            $compteur_global++;
                            //répètes la string "espaces" 4 fois - puis le contenu //pour mise en page
                            echo str_repeat("|&nbsp;&nbsp;&nbsp;&nbsp;", 4)."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nl2br($newContenuLog))."<br><br>";
                        }
                    }
          
                } ;
            }
        }
    } 
}
// si tu as tout lu et rien trouvé suite à l'incrémentation
if($compteur_global == 0){
    echo "Nous ne trouvons pas les associations que vous demandez dans le(s) dossier(s) en référence"."<br><hr>";
}

?>

