<?php

header('Content-Type: text/html; charset=utf-8');

$k = "SO022propa";

if(isset($_POST['key']) && $_POST['key']==$k){

    $jour = date('d');
    $mois = date('m');
    $annee = date('Y');
    $heure = date('H:i:s');

    @mkdir(__DIR__."/logs/".$annee."/".$mois."/",0777,true);

    $_encodeVar = "Log initialisé le:  $jour"/".$mois."/".$annee .'à' .$heure";
    if(!is_file(__DIR__."/logs/".$annee."/".$mois."/".$jour.".log")) {

        file_put_contents(__DIR__."/logs/".$annee."/".$mois."/".$jour.".log", 
        '<b>' .  utf8_decode( $_encodeVar)."</b>\r\n\r\n");

    }  

    $log = "\r\n[".$heure."] ";
    foreach($_POST as $g => $v){

        if($g=='key') continue;
        $g = htmlspecialchars($g);

        $log.=" // ".$g.": ".$v;

    }

    if($log != "[".$heure."] ") file_put_contents(__DIR__."/logs/".$annee."/".$mois."/".$jour.".log", utf8_decode($log),FILE_APPEND);

}

?>