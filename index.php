<?php

use BasicSearch\Controllers\SearchController;
use BasicSearch\RankingAlgorithms\BasicRankUnordered;

require_once "app/start.php";


// Get the arguments passed on the command line

if (count($argv) !== 2) {
    // Condition: Unexpected number of parameters
    $strAux = "1510657932: Usage: php index.php <pathToDirectoryContainingTextFiles>";
    throw new \Exception($strAux);
}

// If we've reached this point, the arguments seem to be correct
$path = $argv[1];

try {
    $strategy = new BasicRankUnordered();
    $search   = new SearchController($path, $strategy);

    $ext      = $search->getDefaultExtension();
    $pathC    = $search->getPath();



    $search->setCaseSensitive(false);
    $search->setIncluded(false);
    $search->setWordRecognitionPattern('/\P{L}+/u');
    $search->setFileExtension(".txt");
    $search->buildIndices();
    $numFiles = $search->getFileCount();

    $msg   = "Found $numFiles '$ext' file(s) in [$pathC]";

    echo PHP_EOL . $msg . PHP_EOL;

    do {
        $line = readline("search> ");
        $line = trim($line . "");

        if (strlen($line) > 0) {
            // Condition: Some input
            if ($line === ":quit") {
                break;
            } else {
                $results = $search->getRanks($line);

                if (count($results) === 0) {
                    // Condition: No matches found
                    echo "   No matches found" . PHP_EOL;
                } else {
                    // Condition: Some matches found
                    foreach($results as $file => $ranking) {
                        $strAux = $ranking;
                        $strAux = str_pad($strAux, 6, " ", STR_PAD_LEFT);
                        echo "   " . $strAux . " % -> " . $file . PHP_EOL;
                    }
                }
            }
        }

    } while (true);

} catch (\Exception $e) {
    $strAux = "1510660645: Unable to search: " . $e->getMessage();
    die ($strAux);
}
