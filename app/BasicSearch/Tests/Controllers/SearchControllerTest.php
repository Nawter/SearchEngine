<?php
    
namespace BasicSearch\Tests\Controllers;

use BasicSearch\Interfaces\RankingInterface;
use BasicSearch\RankingAlgorithms\YourRankingAlgorithm;
use PHPUnit\Framework\TestCase;
use BasicSearch\Controllers\SearchController;
use BasicSearch\RankingAlgorithms\BasicRankUnordered;

/**
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class SearchControllerTest
 *
 */
class SearchControllerTest extends TestCase
{
    /** @var string Where to find text files */
    private $dataFileFolder = __DIR__ . "/../Data/";

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     */
    public function extensionCountProvider()
    {
        $result = [
            [".txt", 11],
            [".bin",  1],
            [".exe",  0]
        ];

        return $result;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @covers BasicSearch\Controllers\SearchController::__construct
     * @covers BasicSearch\Controllers\SearchController::getFileCount
     * @covers BasicSearch\Controllers\SearchController::findFiles
     *
     * @param string $fileExtension The extension of the file to be processed
     * @param int    $fileCount     How many files of that type are expected to be found
     *
     * @dataProvider extensionCountProvider
     */
    public function testCreation01($fileExtension, $fileCount)
    {
        $ranking = new BasicRankUnordered();
        $search = new SearchController($this->dataFileFolder, $ranking);
        $search->setFileExtension($fileExtension);
        $search->findFiles();
        $files = $search->getFileCount();
        $this->assertEquals($fileCount, $files);
    }

    /**
     * Data provider for basic true and false values
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @return array
     */
    public function trueFalseProvider() : array
    {
        $result = [
            [true],
            [false]
        ];

        return $result;
    }
    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @covers BasicSearch\Controllers\SearchController::getCaseSensitive
     * @covers BasicSearch\Controllers\SearchController::setCaseSensitive
     *
     * @dataProvider trueFalseProvider
     */
    public function testCaseSensitive($value)
    {
        $ranking = new BasicRankUnordered();
        $search = new SearchController($this->dataFileFolder, $ranking);
        $search->setCaseSensitive($value);
        $this->assertEquals($value, $search->getCaseSensitive());
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @covers BasicSearch\Controllers\SearchController::getIncluded
     * @covers BasicSearch\Controllers\SearchController::setIncluded
     *
     * @dataProvider trueFalseProvider
     */
    public function testIncluded($value)
    {
        $ranking = new BasicRankUnordered();
        $search = new SearchController($this->dataFileFolder, $ranking);
        $search->setIncluded($value);
        $this->assertEquals($value, $search->getIncluded());
    }


    public function wordRecognitionPatternDataProvider() : array
    {
        $result = [
            ['/\P{L}+/u'],
            ['/(\w+)/'],
            ['/\PL+/u'],
            ['/[,.\s;]+/']

        ];

        return $result;
    }
    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @covers BasicSearch\Controllers\SearchController::getWordRecognitionPattern
     * @covers BasicSearch\Controllers\SearchController::setWordRecognitionPattern
     *
     * @dataProvider wordRecognitionPatternDataProvider
     */
    public function testRecognitionPattern($pattern)
    {
        $ranking = new BasicRankUnordered();
        $search  = new SearchController($this->dataFileFolder, $ranking);

        $search->setWordRecognitionPattern($pattern);

        $this->assertEquals($pattern, $search->getWordRecognitionPattern());


    }
    /**
     *
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     * @return array
     */
    public function dirAndStrategyDataProvider() : array
    {
        $algo1 = new BasicRankUnordered();
        $algo2 = new YourRankingAlgorithm();

        $result = [
            ["dir1", $algo1],
            ["dir1", $algo2],
            ["dir2", $algo1],
            ["dir2", $algo2]
        ];

        return $result;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @covers BasicSearch\Controllers\SearchController::setRankingAlgorithm
     * @covers BasicSearch\Controllers\SearchController::getRankingAlgorithm,
     * @dataProvider dirAndStrategyDataProvider
     */
    public function testPathAndStrategy($path, RankingInterface $strategy)
    {
        $search = new SearchController($path, $strategy);

        $this->assertEquals($path, $search->getPath());
        $this->assertEquals($strategy, $search->getRankingAlgorithm());
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     */
    public function findDataProvider() : array
    {
        $f = $this->dataFileFolder;

        $included = true;
        $sensitiv = true;

        $suit01 = [
            'CaseSensitive' => [$f . "/f011.txt" => "100.00"],
            'aseSensitive'  => [$f . "/f011.txt" => "100.00"]
        ];

        $suit02= [
            'christmas'     => [
                $f . "/f000.txt" => "100.00",
                $f . "/f001.txt" => "100.00",
                $f . "/f002.txt" => "100.00",
                $f . "/f004.txt" => "100.00",
                $f . "/f005.txt" => "100.00",
                $f . "/f006.txt" => "100.00",
                $f . "/f007.txt" => "100.00",
                $f . "/f008.txt" => "100.00",
                $f . "/f009.txt" => "100.00",
                $f . "/f010.txt" => "100.00"
            ],
            'Christmas' => [
                $f . "/f000.txt" => "100.00",
                $f . "/f001.txt" => "100.00",
                $f . "/f002.txt" => "100.00",
                $f . "/f004.txt" => "100.00",
                $f . "/f005.txt" => "100.00",
                $f . "/f006.txt" => "100.00",
                $f . "/f007.txt" => "100.00",
                $f . "/f008.txt" => "100.00",
                $f . "/f009.txt" => "100.00",
                $f . "/f010.txt" => "100.00"
            ],
            'ChrIstMa' => [
                $f . "/f000.txt" => "100.00",
                $f . "/f001.txt" => "100.00",
                $f . "/f002.txt" => "100.00",
                $f . "/f004.txt" => "100.00",
                $f . "/f005.txt" => "100.00",
                $f . "/f006.txt" => "100.00",
                $f . "/f007.txt" => "100.00",
                $f . "/f008.txt" => "100.00",
                $f . "/f009.txt" => "100.00",
                $f . "/f010.txt" => "100.00"
            ]
        ];


        $suit03 = [
            'NotIncludedABCDEFGHIJKLMNOPQ' => [
                $f . "/f011.txt" => "100.00",
            ],
            'CaseSensitive' => [
                $f . "/f011.txt" => "100.00",
            ],
            'casesensitive' => [],
            'ABCDEFGHIJKLMNOPQ' => []

        ];

        $suit04 = [
            'casesensitive' => [
                $f . "/f011.txt" => "100.00",
            ],
            'notincludedABCDEFGHIJKLMNOPQ'  => [
                $f . "/f011.txt" => "100.00",
            ]
        ];

        $suit05 = [
            'See if they can do it again' => [
                $f . "/f000.txt" => "100.00",
                $f . "/f001.txt" =>  "71.43",
                $f . "/f005.txt" =>  "42.86",
                $f . "/f006.txt" =>  "42.86",
                $f . "/f007.txt" =>  "42.86",
                $f . "/f008.txt" =>  "42.86",
                $f . "/f009.txt" =>  "42.86",
                $f . "/f010.txt" =>  "42.86",
                $f . "/f002.txt" =>  "28.57",
                $f . "/f004.txt" =>  "14.29"
            ]
        ];

        $result = [
            [ $included,  $sensitiv,  $suit01],
            [ $included, !$sensitiv,  $suit02],
            [!$included,  $sensitiv,  $suit03],
            [!$included, !$sensitiv,  $suit04],
            [!$included, !$sensitiv,  $suit05]
        ];

        return $result;
    }

    /**
     * @author Nicky Santamaria <nicky.web.001@gmail.com>
     *
     * @dataProvider findDataProvider
     *
     * @param bool $included
     * @param bool $sensitive
     * @param array $searchTerms
     */
    public function testFind($included, $sensitive, $searchTerms)
    {
        $wordPattern = '/\P{L}+/u';
        $ranking     = new BasicRankUnordered();
        $search      = new SearchController($this->dataFileFolder, $ranking);
        $search->setWordRecognitionPattern($wordPattern);
        $search->setCaseSensitive($sensitive);
        $search->setIncluded($included);
        $search->setFileExtension(".txt");
        $search->findFiles();
        $search->buildIndices();

        foreach ($searchTerms as $searchTerm => $expected) {
            $result = $search->getRanks($searchTerm);
            $this->assertEquals($expected, $result);
        }

    }

}
