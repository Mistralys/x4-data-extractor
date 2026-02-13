<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Disable CLI output for the extractor
\Mistralys\X4\ExtractedData\Console::setEnabled(false);

// Disable CLI output for the core library (used by extractor)
if(class_exists('\Mistralys\X4\UI\Console')) {
    \Mistralys\X4\UI\Console::setEnabled(false);
}
