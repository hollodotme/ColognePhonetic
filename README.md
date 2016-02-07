[![Build Status](https://travis-ci.org/hollodotme/ColognePhonetic.svg?branch=master)](https://travis-ci.org/hollodotme/ColognePhonetic)
[![Coverage Status](https://coveralls.io/repos/hollodotme/ColognePhonetic/badge.svg?branch=master&service=github)](https://coveralls.io/github/hollodotme/ColognePhonetic?branch=master)
[![Latest Stable Version](https://poser.pugx.org/hollodotme/cologne-phonetic/v/stable)](https://packagist.org/packages/hollodotme/cologne-phonetic) 
[![Total Downloads](https://poser.pugx.org/hollodotme/cologne-phonetic/downloads)](https://packagist.org/packages/hollodotme/cologne-phonetic) 
[![Latest Unstable Version](https://poser.pugx.org/hollodotme/cologne-phonetic/v/unstable)](https://packagist.org/packages/hollodotme/cologne-phonetic) 
[![License](https://poser.pugx.org/hollodotme/cologne-phonetic/license)](https://packagist.org/packages/hollodotme/cologne-phonetic)

# ColognePhonetic

A PHP OOP implementation of the "Kölner Phonetik Index".

This is an adaption of [Andy Theiler](https://github.com/deezaster)'s precedural implementation with some fixes and unit tests. 

## Requirements

* "PHP" >= "5.5"
* "ext-iconv" "*"

## Installation

```bash
composer require "hollodotme/cologne-phonetic" "~1.0.0"
```

## Basic usage

```php
<?php

use hollodotme\ColognePhonetic\ColognePhonetic;

$inputCharset    = 'UTF-8';
$colognePhonetic = new ColognePhonetic( $inputCharset );

# Retrieve index for a single word
$wordIndex = $colognePhonetic->getWordIndex( 'Wort' );

var_dump( $wordIndex );

# Retrieve index for a phrase (multiple words)
# Phrase is split into words, returns an assoc. array with [ "word" => "index" ]
$phraseIndex = $colognePhonetic->getPhraseIndex( 'Ein Satz mit mehreren Wörtern' );

var_dump( $phraseIndex );
```

**Output:**


	string(3) "072"

```
array(5) {
  'Ein' =>
  string(2) "06"
  'Satz' =>
  string(1) "8"
  'mit' =>
  string(2) "62"
  'mehreren' =>
  string(3) "676"
  'Wortern' =>
  string(5) "07276"
}
```

## Algorithm

The "Kölner Phonetik" transliterates every character of a word to a numeric code between "0" und "8", 
considering at most one adjacent character as context. Some rules apply especially to the
beginning of a word (initial sound). This way similar phonems get mapped to the same numeric code.
For example the caracters "W" and "V" are both transliterated to "3".
The phonetic code of "Wikipedia" is "3412". In contrast to the "Soundex-Code" the length of the "Kölner Phonetik Index"
is not restricted.

Character | Context | Code
--------- | ------- | ----
A, E, I, J, O, U, Y | | 0
H | | -
B| | 1
P | not before H | 1
D, T | not before C, S, Z | 2
F, V, W | | 3
P | before H | 3
G, K, Q | | 4
C | initial sound before A, H, K, L, O, Q, R, U, X | 4
C | before A, H, K, O, Q, U, X but not after S, Z | 4
X | not after C, K, Q | 48
L |  | 5
M, N |  | 6
R |  | 7
S, Z |  | 8
C | after S, Z | 8
C | initial sound but not before A, H, K, L, O, Q, R, U, X | 8
C | not before A, H, K, O, Q, U, X | 8
D, T | before C, S, Z | 8
X | after C, K, Q | 8

The fact that "<em>S</em>C" has priority before "C<em>H</em>" is explained in the addition "but not after" on line 10 
in the table above. This rule was not explicitly part of the official publication, but
can be deduced implicitly from the published examples. 

The transliteration happens in 3 steps:

1. Transliteration of characters from left to right like described in the table above.
2. Removing all duplicate codes.
3. Removing of all "0" codes except at the beginning.