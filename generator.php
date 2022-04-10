<?php

$arguments = $_SERVER['argv'];
if (isset($arguments[1])) {
    $basePassword = (string) $arguments[1];
} else {
    echo "Missing base password argument.\n";
    exit(1);
}

if (isset($arguments[2])) {
    $filePath = (string) $arguments[2];
} else {
    echo "Missing file path argument.\n";
    exit(1);
}

$f = fopen($filePath, 'wb');
if ($f === false) {
    echo "Failed to create file.\n";
    exit(1);
}

$begin = new DateTime('01-01-1950');
$end = new DateTime('31-12-2022');

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval, $end);

$dateFormats = ["mdY", "mYd", "dmY", "dYm", "Ymd", "Ydm"];

foreach ($daterange as $date) {
    foreach ($dateFormats as $dateFormat) {
        fwrite($f, $date->format($dateFormat).$basePassword."\n");
        fwrite($f, $basePassword.$date->format($dateFormat)."\n");
    }
}

//09031986
// Append
$number = 0;
while ($number < 2023) {
    $password = $basePassword.$number;
    fwrite($f, $password."\n");
    $number++;
}

// Prepend
$number = 0;
while ($number < 2023) {
    $password = $number.$basePassword;
    fwrite($f, $password."\n");
    $number++;
}

$words = [];
$iteration = 0;
while (($newWord = replace($basePassword, $iteration)) !== null) {
    $words[] = $newWord;
    $iteration++;
}
foreach ($words as $word){
    fwrite($f, $word."\n");
}

function replace(string $basePassword, int $skipIteration = 0)
{
    $replacements = ['o' => '0', 'k' => 'K'];
    $len = strlen($basePassword);
    $newWord = '';
    $iteration = 0;
    for ($i = 0; $i < $len; $i++) {
        $wordLetter = $basePassword[$i];
        foreach ($replacements as $letter => $newLetter) {
            if ($basePassword[$i] === $letter) {
                if ($iteration >= $skipIteration) {
                    $newWord[$i] = $newLetter;
                    $remainingLetters = substr($basePassword, $i + 1);

                    return $newWord.$remainingLetters;
                }
                $iteration++;
            }
        }
        $newWord[$i] = $wordLetter;
    }

    return null;
}

fclose($f);

exit(0);

