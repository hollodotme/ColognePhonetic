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

		$inputWord = isset($words[0]) ? $words[0] : '';

		$wordIndex = $this->getIndex( $inputWord );

		return $wordIndex;
	}

	/**
	 * @param array $words
	 *
	 * @throws ColognePhoneticException
	 */
	private function guardOnlyOneWord( array $words )
	{
		if ( count( $words ) > 1 )
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
		$i    = 0;
		$code = '';
		$word = strtolower( $inputWord );
		$word = preg_replace( '#[^a-z]#i', '', $word );

		if ( empty($word) )
		{
			return '';
		}

		$chars     = str_split( $word );
		$simpleMap = [
			# A, E, I, J, O, U, Y = '0'
			'a' => '0', 'e' => '0', 'i' => '0', 'j' => '0', 'o' => '0', 'u' => '0', 'y' => '0',
			# F, V, W = '3'
			'f' => '3', 'v' => '3', 'w' => '3',
			# G, K, Q = '4'
			'g' => '4', 'k' => '4', 'q' => '4',
			# L = '5'
			'l' => '5',
			# M, N = '6',
			'm' => '6', 'n' => '6',
			# R = '7'
			'r' => '7',
			# S, Z = '8'
			's' => '8', 'z' => '8',
		];

		if ( $chars[0] == 'c' )
		{
			if ( isset($chars[1]) && !in_array( $chars[1], [ 'a', 'h', 'k', 'l', 'o', 'q', 'r', 'u', 'x' ] ) )
			{
				$code = '8';
			}
			else
			{
				$code = '4';
			}

			$i = 1;
		}

		for ( ; $i < strlen( $word ); $i++ )
		{
			$char = $chars[ $i ];
			$prev = isset($chars[ $i - 1 ]) ? $chars[ $i - 1 ] : null;
			$next = isset($chars[ $i + 1 ]) ? $chars[ $i + 1 ] : null;

			if ( isset($simpleMap[ $char ]) )
			{
				if ( $simpleMap[ $char ] != '0' || $i == 0 )
				{
					$code .= $simpleMap[ $char ];
				}
			}

			# B, P = '1'
			if ( in_array( $char, [ 'b', 'p' ] ) )
			{
				# P before H = '3'
				if ( $char == 'p' && $next == 'h' )
				{
					$code .= '3';
				}
				else
				{
					$code .= '1';
				}
			}

			# D, T = '2'
			if ( in_array( $char, [ 'd', 't' ] ) )
			{
				# D, T before C, S, Z = '8'
				if ( in_array( $next, [ 'c', 's', 'z' ] ) )
				{
					$code .= '8';
				}
				else
				{
					$code .= '2';
				}
			}

			if ( $char == 'c' )
			{
				if ( in_array( $next, [ 'a', 'h', 'k', 'o', 'q', 'u', 'x' ] ) )
				{
					if ( in_array( $prev, [ 's', 'z' ] ) )
					{
						$code .= '8';
					}
					else
					{
						$code .= '4';
					}
				}
				else
				{
					$code .= '8';
				}
			}

			if ( $char == 'x' )
			{
				if ( in_array( $prev, [ 'c', 'k', 'q' ] ) )
				{
					$code .= '8';
				}
				else
				{
					$code .= '48';
				}
			}
		}

		return strval( preg_replace( '#(.)\\1+#', '\\1', $code ) );
	}
}
