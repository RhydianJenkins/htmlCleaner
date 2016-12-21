<?php

// load html
$html = file_get_contents('./htmltest.html');

// create a new DomDocument object
$doc = new DOMDocument();

// load the HTML into the DomDocument object (this would be your source HTML)
$doc->loadHTML($html);

removeElement('script', $doc);
removeElement('style', $doc);

// save cleaned html
$cleanHtml = $doc->saveHtml();
$cleanHtml = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $cleanHtml);
file_put_contents('./htmltest.html', $cleanHtml);

function removeElement($tag, $doc) {
    $nodeList = $doc->getElementsByTagName($tag);
    for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
        $node = $nodeList->item($nodeIdx);
        $node->parentNode->removeChild($node);
    }
}