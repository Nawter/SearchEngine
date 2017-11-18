<?php
    
namespace BasicSearch\Libraries;

/**
 * The coordinates of a word are the details where that word can be found. This means
 *     we need the full path of the file, the line where that word has been found and
 *     the column. Line and column are counted from 0
 *
 * @author Nicky Santamaria <nicky.web.001@gmail.com>
 *
 * Class Coordinates
 *
 */
class Coordinates
{
    /** @var  int The line number where the word has been found, starting in 0 */
    private $line;

    /** @var  int The column where the word has been found, starting in 0 */
    private $column;

    /**
     * Initializes this coordinate
     * @param $line
     * @param $column
     */
    public function __construct($line, $column)
    {
        $this->line     = $line;
        $this->column   = $column;
    }
}
