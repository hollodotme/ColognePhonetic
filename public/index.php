<?php
/**
 * @author hollodotme
 */

namespace hollodotme\ColognePhonetic;

require(__DIR__ . '/../vendor/autoload.php');

$colognePhonetic = new ColognePhonetic();

# Retrieve index for a single word
$wordIndex = $colognePhonetic->getWordIndex( 'Wort' );

var_dump( $wordIndex );

# Retrieve index for a phrase (multiple words)
# Phrase is split into words, returns an assoc. array with [ "word" => "index" ]
$phraseIndex = $colognePhonetic->getPhraseIndex( 'Ein Satz mit mehreren Wortern' );

var_dump( $phraseIndex );