<?php
    
namespace BasicSearch\RankingAlgorithms;

use BasicSearch\Interfaces\RankingInterface;

use Ds\Map;

/**
 *
 * Implements a basic ranking algorithm. It will give a ranking of 100% if all the
 *     words passed in the command line are found in the file, not matter the order
 *
 * Conversely, it will give a rank of 0% if none of the words have been found
 *
 * By the same token, it will give an intermediate rank if some of the words are found
 *
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class BasicRank
 *
 */
class BasicRankUnordered implements RankingInterface
{
    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @inheritdoc
     */
    public function rank($text, $indices, $regexp, $caseSensitive, $included, $files) : array
    {
        // Initialize the rankings
        $rankings = [];

        foreach ($files as $file) {
            $rankings[$file] = 0;
        }

        $auxStr = $text;

        // Treat this text in the same way we treat the text files
        if (!$caseSensitive) {
            // Condition: No case sensitive
            $auxStr = strtolower($auxStr);
        }

        // Split the text using the same strategy
        $words = preg_split($regexp, $auxStr);

        foreach($words as $word) {
            // Condition: Not having processed the entire collection
            $files = $this->searchForWord($word, $indices, $included);

            foreach ($files as $file => $coords) {
                $rankings[$file] += 1;
            }
        }

        // Convert the rankings to percentages.
        $oneHundredPercent = count($words);

        if ($oneHundredPercent > 0) {
            foreach($rankings as $file => $wordsPresent) {
                $actualRanking = 100 * $wordsPresent / $oneHundredPercent;
                $rankings[$file] = number_format(round($actualRanking,2), 2);
            }
        }

        // Sort in ascending order
        arsort($rankings);

        // Top 10
        $rankings = array_slice($rankings, 0, 10);

        // Remove ranking = 0
        $result = [];

        foreach ($rankings as $file => $ranking) {
            if ($ranking != 0) {
                // Condition: Some of the words found in this file
                $result[$file] = $ranking;
            }
        }
        return $result;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @inheritdoc
     */
    public function searchForWord($word, Map $indices, $included) : array
    {
        $result = [];

        if ($included) {
            // Condition: The word can be a substring in another word
            $keys = $indices->keys();

            foreach($keys as $key) {
                // Condition: Not having processed the entire collection
                if (strpos($key, $word) !== false) {
                    // Condition: $word is a substring of $key
                    $list = $indices->get($key);

                    foreach($list as $file => $coords) {
                        // We have to merge not only the files but also the coords found. It's cumulative
                        if (isset($result[$file])) {
                            // Condition: The file already exists
                            $result[$file] = array_merge($result[$file], $coords);
                        } else {
                            $result[$file] = $coords;
                        }
                    }
                }
            }

        } else {
            // Condition: Match the entire word
            if ($indices->hasKey($word)) {
                $result = $indices->get($word);
            }
        }

        return $result;
    }


}
