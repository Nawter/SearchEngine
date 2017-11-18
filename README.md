# Exercise description
The exercise is to write a **command line** driven text search engine, usage being: 

`php <filename> <pathToDirectoryContainingTextFiles>`

This should read all the text files in the given directory, building an _in memory_ representation of the files and their contents, and then give a command prompt at which interactive searches can be performed.

An example session might look like: 

    $ php simplesearch.php /foo/bar
    14 files read in directory /foo/bar
    search>
    search> to be or not to be
        filename1: 100%
        filename2: 95%
    search>
    search> cats
        no matches found
    search> :quit
    $

I.e. the search should take the words given on the command prompt and return a list of the top 10(max) matching filenames in rank order, giving the rank score against each match.

Note: Treat the above as an outline spec; you don't need to exactly reproduce the above output. 

**Ranking**
- [X] The rank score must be 100% if a file contains all the words
- [X] It must be 0% if it contains none of the words
- [X] It should be between 0 and 100 if it contains only some of the words, but the exact ranking formula is up to you to choose and implement.

**Things to consider in your implementation**
- [X] What constitutes a _word_.
- [X] What constitutes two words being equal (and matching)
- [X] Data structure design: the _in memory_ representation to search against.
- [X] Ranking score design: start with something basic, then iterate as time allows
- [X] Testability

**Deliverables**
- [X] Code to implement a version of the above.
- [X] A `README` containing instructions so that we known how to build and run your code.

# Resolution
The sections below will describe the approach to resolve the exercise

## Build the application
Run the script `build.sh` in the root dir of the project

Make sure you have `composer` installed to run globally

## Usage
execute `php index.php <fullpath>`

Notice how the class `SearchController` is configured before doing any search, in the file `index.php` (the entry point of the application)

The application will run in a loop, and will ask for search terms and print the results continuously. 

To exit the loop type `:quit`

## Tests
Execute `php vendor/bin/phpunit`

## Notes
1. The specs were a bit ambiguous regarding the process to be implemented. I could have use Elastic Search to perform all the searches, but that would require a bit of configuration in your side in order to make this script to work. 
1. Also, the specs mention _words contained_ in the file, but doesn't mention to retrieve the position of the words where they have been found, which would be the logic approach. 
1. I implemented the _inverted index text search_, so each occurrence of a word would be pinpointed by the file name, the line and the column where it has been found. Check class in  `./app/BasicSearch/Libraries/Coordinates.php`, which is used in the search algorithm (although not used to print the results as it was not required). 
1. I also allowed some degree of modification in order to identify what could represent _a word_, meaning how each file is going to be processed. To achieve that, the search engine will allow to pass a regular expression in order to allow different approaches for processing each file. 
1. The ranking strategy may need to undergo modifications and/or extensions in the future. This is why I implemented the **Strategy Design Pattern**. For this implementation, I used the class `BasicRankUnordered`. If other approach is needed, you may need to implement the interface `RankingInterface`, and pass an instance of this new class to the search engine when it is instantiated (see how it is done in `index.php`). For that new class, it is necessary to implement the logic of the search and the ranking formula. The class `YourRankingAlgorithm` has been left as proof of concept on how the changes in the ranking strategy are possible and easy to achieve thanks to this design pattern.
1. The library Data Structures (Ds) have been used in this script, more specifically the Map data structure. It has been included in the `composer.json file`.
1. No frameworks have been used (e.g. _Symfony_). The code follows PSR-IV standards and the classes are auto-loaded using the composer optimized autoload.

## Memory Structure
The index structure kept in memory for each session can be described as follows:

1. A **Map** is build for each session
1. The _key_ part of the map is the word extracted from each file, according to the selected regexp.  
1. The _value_ part of the map is an associative array
1. The **left side** of the associative array is the file where the word has been found, identified by its full path
1. The **right side** of the associative array is another array, having as elements objects type `Coordinates`, which pinpoint exactly where the word has been found (line and column)

## Future extensions
1. The same way the command `:quit` has been implemented, it would be very easy to implement some other commands such as:
    1. Change the regular expressions
    1. To activate or deactivate the case sensitive mode
    1. To activate or deactivate the substring match
    1. To change the directory
    1. etc.
1. Implementation of other ranking strategies (as mentioned above)    