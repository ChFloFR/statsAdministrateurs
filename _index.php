<?php
$pathLen = 0;
$maxDays = 90;
function prePad($level)
{
    $ss = "";
    for ($ii = 0; $ii < $level; $ii++) {
        $ss = $ss . "|&nbsp;&nbsp;";
    }
    return $ss;
}
function myScanDir($dir, $level, $rootLen,$maxDays)
{
    global $pathLen;
    if ($handle = opendir($dir)) {
        $allFiles = array();
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (is_dir($dir . "/" . $entry)) {
                    $allFiles[] = "D: " . $dir . "/" . $entry;
                } else {
                    $allFiles[] = "F: " . $dir . "/" . $entry;
                }
            }
        }
        closedir($handle);
        natsort($allFiles);
        foreach ($allFiles as $value) {
            $displayName = substr($value, $rootLen + 4);
            $fileName    = substr($value, 3);
            $linkName    = str_replace(" ", "%20", substr($value, $pathLen + 3));
            if (is_dir($fileName)) {
                echo prePad($level) . $linkName . "<br>\n";
                myScanDir($fileName, $level + 1, strlen($fileName),$maxDays);
            } else {
                //echo prePad($level) . "<a href=\"" . $linkName . "\" style=\"text-decoration:none;\">" . $displayName . "</a><br>\n";
                $data = file_get_contents(__DIR__."/logs".$linkName);
                echo '<br>'.prePad(4).utf8_encode(nl2br($data))."<br><br>";
                $maxDays--;
                if($maxDays<=0) break;
            }
        }
    }
}

?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Propagest 4</title>
</head>

<body>
    <h4>Propagest 4 - logs</h4>
    <p style="font-family:'Courier New', Courier, monospace; font-size:small;">
        <?php
       $root = __DIR__.'/logs/';
        $pathLen = strlen($root);
        myScanDir($root, 0, strlen($root),$maxDays); 
        ?>
    </p>
</body>

</html>