<?php
    
namespace BasicSearch\Controllers;

use \Exception;
use BasicSearch\Interfaces\RankingInterface;

/**
 *
 * Implements the sufficient logic for the search
 *
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class SearchController
 *
 */
class SearchController extends AbstractSearch
{
    /**
     *
     * @param string $path Where to find the text files
     * @param RankingInterface $strategy How to rank the matches
     * @throws Exception if no files found
     */
    public function __construct($path, RankingInterface $strategy)
    {
        $this->path = $path;

        $this->setRankingAlgorithm($strategy);
    }

}
