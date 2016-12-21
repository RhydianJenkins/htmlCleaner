<?php

// Select the root, and all files
// Note, must be a directory and does not do sub directories
if (empty($argv[1]) || !is_dir($argv[1])) {
    die('No directory given. Exiting...' . PHP_EOL);
} else {
    $dir = $argv[1];
}

// Clean and save all .html files in given path
foreach(glob($dir . '*.htm') as $file) {
    cleanAndSave($file);
    echo 'Cleaned ' . $file . PHP_EOL;
}

function cleanAndSave($filename) {
    // load html
    $html = file_get_contents($filename);

    // create a new DomDocument object
    $doc = new DOMDocument();

    // load the HTML into the DomDocument object (this would be your source HTML)
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    libxml_use_internal_errors(false);

    // remove the script and style elements
    removeElement('script', $doc);
    removeElement('style', $doc);
    removeElement('class', $doc);

    // remove inline styles
    $cleanHtml = $doc->saveHtml();
    $cleanHtml = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $cleanHtml);

    // save cleaned html
    file_put_contents($filename, $cleanHtml);
}

function removeElement($tag, $doc) {
    $nodeList = $doc->getElementsByTagName($tag);
    for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
        $node = $nodeList->item($nodeIdx);
        $node->parentNode->removeChild($node);
    }
}