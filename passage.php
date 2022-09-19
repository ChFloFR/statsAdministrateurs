<?php 

$a = 2;

$b = &$a;

$b = 8;

echo '$a = ' . $a;
echo '<br>$b = ' . $b;