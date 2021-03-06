<?php

// Get user options
$shortOpts = 'a::'; // auto save - optional
$options = getopt($shortOpts);

// Get which dir to clean from argv
if ($argc == 0  || empty($argv[$argc-1])) {
    die('No directory given. Usage: "php clean.php [OPTIONS] <DIR_NAME>"' . PHP_EOL);
} elseif (!is_dir($argv[$argc-1])) {
    die($argv[$argc-1] . ' is not a directory. Exiting...' . PHP_EOL);
} else {
    $dir = $argv[$argc-1];
    // amend '/' to end of $dir if it isnt there already
    if (substr($dir, -1) != '/') {
        $dir .= '/';
    }
}

// Init empty array of files to save
$cleanedFiles = array();

// Clean and save all .htm files in given path
foreach(glob($dir . '*.htm') as $file) {
    $html = file_get_contents($file);
    $cleanedHtml = clean($html);
    if ($html != $cleanedHtml) {
        $cleanedFiles[$file] = $cleanedHtml;
        echo 'Cleaned ' . $file . PHP_EOL;
    }
}

// Clean and save all .html files in given path
foreach(glob($dir . '*.html') as $file) {
    $html = file_get_contents($file);
    $cleanedHtml = clean($html);
    if ($html != $cleanedHtml) {
        $cleanedFiles[$file] = $cleanedHtml;
        echo 'Cleaned ' . $file . PHP_EOL;
    }
}

// Check if any files have been cleaned
if (empty($cleanedFiles)) {
    die('No .htm or .html files found that needed cleaning.' . PHP_EOL);
}

// If the user didnt want auto save
if (!isset($options['a'])) {
    // If the user didnt want to save the files, exit without saving
	if (PHP_OS == 'WINNT') {
		echo "[W] Save cleaned files? [y/n]: ";
		if (strtolower(stream_get_line(STDIN, 1024, PHP_EOL)) != 'y') {
			die('Changes have not been saved.' . PHP_EOL);
		}
	} else {
		if (strtolower(readline('Save cleaned files? [y/n]: ')) != 'y') {
			die('Changes have not been saved.' . PHP_EOL);
		}	
	}
}

// Save the changes
save($cleanedFiles);

/**
 * Cleans a given file and returns the cleaned html
 */
function clean($html) {
    // create a new DomDocument object
    $doc = new DOMDocument();

    // load the HTML into the DomDocument object (this would be your source HTML)
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $doc->formatOutput = true;
    $doc->preserveWhitespace = false;
    libxml_use_internal_errors(false);

    // remove the script and style elements
    removeElement('script', $doc);
    removeElement('style', $doc);

    // remove inline styles
    $cleanHtml = $doc->saveHtml();
    $cleanHtml = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $cleanHtml);

    // Return cleaned html
    return $cleanHtml;
}

/**
 * Removes the string $tag element from the DOMDocument $doc
 */
function removeElement($tag, $doc) {
    $nodeList = $doc->getElementsByTagName($tag);
    for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
        $node = $nodeList->item($nodeIdx);
        $node->parentNode->removeChild($node);
    }
}

/**
 * Takes in array as [$filePath => $fileContents] structure
 */
function save(array $filesToSave) {
    foreach ($filesToSave as $filePath => $fileContents) {
        file_put_contents($filePath, $fileContents);
    }

    // Echo out user info
    echo 'Files saved.' . PHP_EOL;
}