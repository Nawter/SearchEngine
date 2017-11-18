<?php
    
namespace BasicSearch\Controllers;

use BasicSearch\Interfaces\RankingInterface;
use BasicSearch\Libraries\Coordinates;
use Ds\Map;

/**
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class AbstractSearch
 *
 */
class AbstractSearch
{
    /** @var  RankingInterface*/
    protected $rankingAlgorithm;

    /** @var string Default extension of the text files */
    protected $DEF_EXT = ".txt";

    /** @var  string */
    protected $path;

    /** @var array The files of the $DEF_EXT found in $path */
    protected $files;

    /** @var bool Will determine if the match is based on the case */
    protected $caseSensitive = false;

    /** @var bool Will determine if a search word can match a substring */
    protected $included = false;

    /** @var Map */
    protected $indices;

    /** @var string Regexp to determine "what's a word" */
    protected $strategy = '/\P{L}+/u';

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return RankingInterface
     */
    public function getRankingAlgorithm() : RankingInterface
    {
        return $this->rankingAlgorithm;
    }
    /**
     * Will set the strategy for ranking files
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     */
    public function setRankingAlgorithm(RankingInterface $strategy)
    {
        $this->rankingAlgorithm = $strategy;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return bool
     */
    public function getCaseSensitive() : bool
    {
        return $this->caseSensitive;
    }
    /**
     * Sets the extension of the files to be processed.
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return void
     */
    public function setFileExtension($fileExtension)
    {
        $this->DEF_EXT = $fileExtension;
    }

    /**
     * Will find the files according to the path and extension
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return void
     * @throws \Exception if no files to be found
     */
    public function findFiles()
    {
        // Search for the files
        $this->files = glob($this->path . "/*" . $this->DEF_EXT);

        if (count($this->files) === 0) {
            // Condition: nothing to search for
            $this->files = [];
        }
    }
    /**
     * Will build the indices for the search. Override in the descendant class in case
     *    it need changing
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return void
     */
    public function buildIndices()
    {
        // Search for files
        $this->findFiles();

        // Scrap the existing indices, if any

        if ($this->indices) {
            // Condition: indices already built
            $this->indices->clear();
        } else {
            // Condition: indices not yet initialized
            $this->indices = new Map();
        }

        for ($i = 0; $i < count($this->files); $i++) {
            // Condition: Not having processed the entire collection of files
            $this->buildIndex($this->files[$i]);
        }
    }

    /**
     * Will build the index for the file passed as parameter
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * Override it in the descendant class in case you need a different implementation
     *
     * @return void
     * @throws \Exception if the file can't be open
     */
    public function buildIndex($file)
    {
        $handle = fopen($file, "r");

        if ($handle) {
            $lineCount = 0;
            while (($line = fgets($handle)) !== false) {
                // Condition: Not having read the entire file
                $auxStr = $line;

                // Pre-process the line according to our preferences
                if (!$this->caseSensitive) {
                    // Condition: Don't care about the case
                    $auxStr = strtolower($auxStr);
                }

                // Separate using white spaces, tabs and carrier returns
                // $words = preg_split('/\s+/', $auxStr);
                $words = preg_split($this->strategy, $auxStr);

                $pointer = 0;

                foreach($words as $word) {
                    // Condition: Not having processed this collection of words

                    if (strlen(trim($word . "")) > 0) {
                        // Condition: Not empty word
                        $col = strpos($auxStr, $word, $pointer);

                        if ($col !== false) {
                            // Condition: word found
                            $pointer = $col + strlen($word);

                            $coordinate = new Coordinates($lineCount, $col);
                            // Store the coordinates
                            if (!$this->indices->hasKey($word)) {
                                // Condition: Not yet indexed
                                $list[$file][] = $coordinate;
                            } else {
                                // Condition: Already indexed
                                $list = $this->indices->get($word);
                                $list[$file][] = $coordinate;
                            }

                            $this->indices->put($word, $list);
                            unset($list);
                        }
                    }
                }

                $lineCount ++;
            }
        } else {
            // Condition: Error reading the file
            $strAux = "1510661854: Unable to open file $file";
            throw new \Exception($strAux);
        }
    }
    /**
     * Will get the ranks using the selected ranking method
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @param string $text The text passed in the command line
     *
     * @return array. It returns an associative array as follows:
     *     [
     *         "fullpath1" => rank1,
     *         "fullpath2" => rank2,
     *         ...
     *         "fullPathN" => rankN
     *     ]
     */
    public function getRanks($text) : array
    {
        $result = $this->rankingAlgorithm->rank(
            $text,
            $this->indices,
            $this->strategy,
            $this->caseSensitive,
            $this->included,
            $this->files
        );

        return $result;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return void
     */
    public function setIncluded($included)
    {
        $this->included = $included;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return bool
     */
    public function getIncluded() : bool
    {
        return $this->included;
    }
    /**
     * Will set whether to consider the word case for a match
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return void
     */
    public function setCaseSensitive($caseSensitive)
    {
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return string the path where the files reside
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Returns the number of files with extension $DEF_EXT found in $path
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return int
     */
    public function getFileCount() : int
    {
        $result = count($this->files);

        return $result;
    }

    /**
     * Returns the default file extension that will be processed
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return string
     */
    public function getDefaultExtension() : string
    {
        $result = $this->DEF_EXT;

        return $result;
    }

    /**
     * Will determine what is a word, based on a regexp
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @param string $strategy
     */
    public function setWordRecognitionPattern($strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return string
     */
    public function getWordRecognitionPattern() : string
    {
        return $this->strategy;
    }
}
