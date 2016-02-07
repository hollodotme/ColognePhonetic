<?php
/**
 * @author hollodotme
 */

namespace hollodotme\ColognePhonetic;

use hollodotme\ColognePhonetic\Exceptions\ColognePhoneticException;

/**
 * Class ColognePhonetic
 * @package hollodotme\ColognePhonetic
 */
final class ColognePhonetic
{
	/** @var string */
	private $inputCharset;

	/**
	 * @param string $inputCharset
	 */
	public function __construct( $inputCharset = 'UTF-8' )
	{
		$this->inputCharset = $inputCharset;
	}

	/**
	 * @param string $word
	 *
	 * @return string
	 */
	public function getWordIndex( $word )
	{
		$words = $this->getWords( $word );

		$this->guardOnlyOneWord( $words );

		$wordIndex = $this->getIndex( $words[0] );

		return $wordIndex;
	}

	/**
	 * @param array $words
	 *
	 * @throws ColognePhoneticException
	 */
	private function guardOnlyOneWord( array $words )
	{
		if ( count( $words ) != 1 )
		{
			throw new ColognePhoneticException(
				'More than one word given. Use ColognePhonetic::getPhraseIndex to get index of multiple words.',
				100
			);
		}
	}

	/**
	 * @param string $phrase
	 *
	 * @return array
	 */
	public function getPhraseIndex( $phrase )
	{
		$wordsWithIndex = [ ];
		$words          = $this->getWords( $phrase );

		foreach ( $words as $word )
		{
			$wordsWithIndex[ $word ] = $this->getIndex( $word );
		}

		return $wordsWithIndex;
	}

	/**
	 * @param string $phrase
	 *
	 * @return array
	 */
	private function getWords( $phrase )
	{
		$asciiPhrase = $this->transliterateToAscii( $phrase );

		return preg_split( "#[\W_]#i", $asciiPhrase, -1, PREG_SPLIT_NO_EMPTY );
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	private function transliterateToAscii( $string )
	{
		$asciiString = iconv( $this->inputCharset, 'US-ASCII//TRANSLIT//IGNORE', $string );

		return $asciiString;
	}

	/**
	 * @param string $inputWord
	 *
	 * @return string
	 */
	private function getIndex( $inputWord )
	{
		$code    = '';
		$word    = strtolower( $inputWord );
		$word    = preg_replace( '#[^a-z]#i', '', $word );
		$wordlen = strlen( $word );
		$chars   = str_split( $word );

		if ( empty($chars) )
		{
			return '';
		}

		if ( $chars[0] == 'c' )
		{
			if ( isset($chars[1]) )
			{
				switch ( $chars[1] )
				{
					case 'a':
					case 'h':
					case 'k':
					case 'l':
					case 'o':
					case 'q':
					case 'r':
					case 'u':
					case 'x':
						$code = '4';
						break;
					default:
						$code = '8';
						break;
				}
			}
			else
			{
				$code = '4';
			}

			$x = 1;
		}
		else
		{
			$x = 0;
		}

		for ( ; $x < $wordlen; $x++ )
		{
			switch ( $chars[ $x ] )
			{
				case 'a':
				case 'e':
				case 'i':
				case 'j':
				case 'o':
				case 'u':
				case 'y':
					$code .= '0';
					break;
				case 'b':
				case 'p':
					$code .= '1';
					break;
				case 'd':
				case 't':
				{
					if ( $x + 1 < $wordlen )
					{
						switch ( $chars[ $x + 1 ] )
						{
							case 'c':
							case 's':
							case 'z':
								$code .= '8';
								break;
							default:
								$code .= '2';
								break;
						}
					}
					else
					{
						$code .= '2';
					}
					break;
				}
				case 'f':
				case 'v':
				case 'w':
					$code .= '3';
					break;
				case 'g':
				case 'k':
				case 'q':
					$code .= '4';
					break;
				case 'c':
				{
					if ( $x + 1 < $wordlen )
					{
						switch ( $chars[ $x + 1 ] )
						{
							case 'a':
							case 'h':
							case 'k':
							case 'o':
							case 'q':
							case 'u':
							case 'x':
								switch ( $chars[ $x - 1 ] )
								{
									case 's':
									case 'z':
										$code .= '8';
										break;
									default:
										$code .= '4';
								}
								break;
							default:
								$code .= '8';
								break;
						}
					}
					else
					{
						$code .= '8';
					}
					break;
				}
				case 'x':
				{
					if ( $x > 0 )
					{
						switch ( $chars[ $x - 1 ] )
						{
							case 'c':
							case 'k':
							case 'q':
								$code .= '8';
								break;
							default:
								$code .= '48';
						}
					}
					else
					{
						$code .= '48';
					}
					break;
				}
				case 'l':
					$code .= '5';
					break;
				case 'm':
				case 'n':
					$code .= '6';
					break;
				case 'r':
					$code .= '7';
					break;
				case 's':
				case 'z':
					$code .= '8';
					break;
			}
		}

		$codelen      = strlen( $code );
		$codeChars    = str_split( $code );
		$phoneticcode = $codeChars[0];

		for ( $x = 1; $x < $codelen; $x++ )
		{
			if ( $codeChars[ $x ] != '0' )
			{
				$phoneticcode .= $codeChars[ $x ];
			}
		}

		return strval( preg_replace( '#(.)\\1+#', '\\1', $phoneticcode ) );
	}
}
