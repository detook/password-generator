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
$end = $end->modify('+1 day');

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval, $end);

foreach ($daterange as $date) {
    $dateFormats = ["mdY", "mYd", "dmY", "dYm", "Ymd", "Ydm"];
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

//l => 1
//o => 0
$countChars = count_chars($basePassword);
$chars = [
    'o' => '0',
    'O' => '0',
    'l' => '1',
];
foreach ($chars as $char => $replacement) {
    if ($countChars[ord($char)] === 0) {
        continue;
    }

    for ($i = 0; $i < $countChars[ord($char)]; $i++) {
        $password = preg_replace('/'.preg_quote($char, '/').'/', $replacement, $basePassword, $i + 1);
        fwrite($f, $password."\n");
    }
}

fclose($f);

exit(0);

