<?php
// this script goes through a ical file to create one file per day
// Only interest is VEVENTS, and adding them to a new or existing file for the day concerned. Multiday events are ignored for now
// In its first run keeping it basic
// written by Ton Zijlstra https://www.zylstra.org/blog, public domain

// open calfile

if ($file = fopen("tonwerkkopie.ics", "r")) {
    while(! feof($file)) {
    $line = fgets($file); // read a line
    if (startsWith($line, 'BEGIN:VEVENT')) { // we've got an event
       $locatie="";
       $omschrijving="";
       $samenvatting="";
       while(! startsWith($line, 'END:VEVENT')) {
       $line = fgets($file); // read a line
       // if DTSTART
       if (startsWith($line, 'DTSTART:')) {
       $dtstart=substr($line, 8);
       $syear=substr($dtstart, 0,4);
       $smonth=substr($dtstart, 4,2);
       $sday=substr($dtstart, 6,2);
       $stime=substr($dtstart, 9,4);
       }
       if (startsWith($line, 'DTSTART;')) {
       $dtstart=substr($line, 19);
       $syear=substr($dtstart, 0,4);
       $smonth=substr($dtstart, 4,2);
       $sday=substr($dtstart, 6,2);
       $stime="hele dag";
       }
       // if DTEND
       if (startsWith($line, 'DTEND:')) {
       $dtend=substr($line, 6);
       $eyear=substr($dtend, 0,4);
       $emonth=substr($dtend, 4,2);
       $eday=substr($dtend, 6,2);
       $etime=substr($dtend, 9,4);
       }
       if (startsWith($line, 'DTEND;')) {
       $dtend=substr($line, 17);
       $eyear=substr($dtend, 0,4);
       $emonth=substr($dtend, 4,2);
       $eday=substr($dtend, 6,2);
       $etime="";
       }
       // if SUMMARY
       if (startsWith($line, 'SUMMARY:')) {
       $samenvatting=substr($line, 8, -1);
       $samenvatting = str_replace(array("\r", "\n"), '', $samenvatting);
       }
       // if DESCRIPTION
       if (startsWith($line, 'DESCRIPTION:')) {
       $omschrijving=substr($line, 12);
       $omschrijving = str_replace(array("\r", "\n"), '', $omschrijving);
       }
       // als LOCATION
       if (startsWith($line, 'LOCATION:')) {
       $locatie=substr($line, 9, -1);
       $locatie = str_replace(array("\r", "\n"), '', $locatie);
       }
       // ignore other lines or content
       } // end while event
       // with all content available construct day log
       $daglog="Daglog ".$sday."-".$smonth."-".$syear.".md"; //title in the structure I use them
       $activiteit="- ".$stime;
       if ($etime<>"") $activiteit=$activiteit."-".$etime;
       $activiteit=$activiteit." ".$samenvatting;
       if ($locatie<>"") $activiteit=$activiteit." (in ".$locatie.")";
       if ($omschrijving<>"") $activiteit=$activiteit." ".$omschrijving;
       schrijfdag($syear, $smonth, $sday, $daglog, $activiteit);
       $daglog="";
       $activiteit="";
    } //end if reading 1 event
    } // end while eof
    } // end reading file file
    fclose($file);
    
function schrijfdag($jaar, $maand, $dag, $titel, $lijnactiviteit) {
$schrijffile = $lijnactiviteit." \n";
// test if file exists
$fileloc="./".$jaar."/".$maand."/".$titel; // this follows my folder structure
if (!file_exists($fileloc)){
// if not existing then new file with my header etc
$filebegin ="#".$jaar."/".$maand."/".$dag."\n \n# Gedaan \n";
$schrijffile =$filebegin.$lijnactiviteit." \n";
 }
// if it does exsit append new line
if ($jaar=="/Ams") echo $schrijffile." gaat niet goed<br/>";
if ($jaar=="/Par") echo $schrijffile." gaat niet goed<br/>";
file_put_contents($fileloc, $schrijffile, FILE_APPEND);
}

function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}
?>