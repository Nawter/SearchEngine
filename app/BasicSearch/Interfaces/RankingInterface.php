<?php
    
namespace BasicSearch\Interfaces;

use Ds\Map;

/**
 * Will serve to implement the Strategy pattern for ranking the files
 *
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class AbstractRanking
 *
 */
interface RankingInterface
{
    /**
     * All strategies to rank files must implement this function
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @param string $text The text passed in the command line
     * @param Map    $indices The indices that have been built from the text files
     * @param string $regexp  The regular expression that has been used to tokenize
     *     words from the input files
     * @param bool $caseSensitive Whether we should take into account the case of a
     *     word in order to consider it a match
     * @param bool $included Whether to consider a substring as a match of another word
     * @param array $files List of the full paths of files to be processed
     *
     * @return array. It returns an associative array as follows:
     *     [
     *         "fullpath1" => rank1,
     *         "fullpath2" => rank2,
     *         ...
     *         "fullPathN" => rankN
     *     ]
     *
     */
    public function rank($text, $indices, $regexp, $caseSensitive, $included, $files) : array;

    /**
     * Will check if our indices contain the word passed as parameter
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @param string $word The word to be searched
     *
     * @param Map $indices The indices that have been built
     *
     * @param bool $included Whether we would consider a substring to be a match
     *
     *
     * @return array List of files containing that word
     */
    public function searchForWord($word, Map $indices, $included) : array;
}
