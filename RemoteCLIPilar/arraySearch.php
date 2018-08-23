<?php
$file = "DATABASE.config";
$f = fopen($file, "r");
$start = false;
while ($line = fgets($f, 1000)) {
  $arrayLine = explode('=', $line);
  $lineParams = $arrayLine[0];
  $lineValue = $arrayLine[1];
  if($lineParams == "HOST")echo  "HOST OK => $lineValue";
  if($lineParams == "USER")echo  "USER OK => $lineValue";
  if($lineParams == "PASSWORD")echo  "PASSWORD OK => $lineValue";
  if($lineParams == "DATABASENAME")echo  "DATABASENAME OK => $lineValue ";
}


?>
