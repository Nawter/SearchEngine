<?php
    
namespace BasicSearch\RankingAlgorithms;

use BasicSearch\Interfaces\RankingInterface;
use Ds\Map;

/**
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class YourRankingAlgorithm
 *
 */
class YourRankingAlgorithm implements RankingInterface
{
    /**
     * Your version of ranking
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @inheritdoc
     */
    public function rank($text, $indices, $regexp, $caseSensitive, $included, $files) : array
    {
        $result = [];

        return $result;
    }

    /**
     * Your version of searching
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @inheritdoc
     */
    public function searchForWord($word, Map $indices, $included) : array
    {
        $result = [];

        return $result;
    }
}
