<?php
/**
 * @author hollodotme
 */

namespace hollodotme\ColognePhonetic\Tests\Unit;

use hollodotme\ColognePhonetic\ColognePhonetic;

/**
 * Class ColognePhoneticTest
 * @package hollodotme\ColognePhonetic\Tests\Unit
 */
class ColognePhoneticTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \hollodotme\ColognePhonetic\Exceptions\ColognePhoneticException
	 * @expectedExceptionCode 100
	 */
	public function testGetWordIndexThrowsExceptionIfMoreThanOneWordIsGiven()
	{
		$testWords       = 'Mehr als ein Wort';
		$colognePhonetic = new ColognePhonetic();

		$colognePhonetic->getWordIndex( $testWords );
	}

	public function testGetWordIndexIsCaseInsensitive()
	{
		$lowerCaseWord = 'wort';
		$upperCaseWord = 'WORT';

		$colognePhonetic = new ColognePhonetic();

		$this->assertEquals(
			$colognePhonetic->getWordIndex( $lowerCaseWord ),
			$colognePhonetic->getWordIndex( $upperCaseWord )
		);
	}

	public function testGetPhraseIndexIsCaseInsensitive()
	{
		$lowerCasePhrase = 'ein satz mit mehreren wörtern';
		$upperCasePhrase = 'EIN SATZ MIT MEHREREN WÖRTERN';

		$colognePhonetic = new ColognePhonetic();

		$lowerCaseIndex = $colognePhonetic->getPhraseIndex( $lowerCasePhrase );
		$upperCaseIndex = $colognePhonetic->getPhraseIndex( $upperCasePhrase );

		$this->assertEquals(
			array_values( $lowerCaseIndex ),
			array_values( $upperCaseIndex )
		);
	}

	/**
	 * @param string $char
	 * @param string $expectedCode
	 *
	 * @dataProvider singleCharacterProvider
	 */
	public function testSingleCharacterTransliteration( $char, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $char );

		$this->assertEquals( $expectedCode, $index );
	}

	public function singleCharacterProvider()
	{
		return [
			# A, E, I, J, O, U, Y = '0'
			[ 'A', '0' ],
			[ 'E', '0' ],
			[ 'I', '0' ],
			[ 'J', '0' ],
			[ 'O', '0' ],
			[ 'U', '0' ],
			[ 'Y', '0' ],
			# H = ''
			[ 'H', '' ],
			# B, P = '1'
			[ 'B', '1' ],
			[ 'P', '1' ],
			# D, T = '2'
			[ 'D', '2' ],
			[ 'T', '2' ],
			# F, V, W = '3'
			[ 'F', '3' ],
			[ 'V', '3' ],
			[ 'W', '3' ],
			# G, K, Q = '4'
			[ 'G', '4' ],
			[ 'K', '4' ],
			[ 'Q', '4' ],
			# C = '4'
			[ 'C', '4' ],
			# X = '48'
			[ 'X', '48' ],
			# L = '5',
			[ 'L', '5' ],
			# M, N
			[ 'M', '6' ],
			[ 'N', '6' ],
			# R = '7'
			[ 'R', '7' ],
			# S, Z = '8'
			[ 'S', '8' ],
			[ 'Z', '8' ],
		];
	}

	/**
	 * @param string $word
	 * @param string $expectedCode
	 *
	 * @dataProvider cRuleProvider
	 */
	public function testCheckSpecialRulesForC( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	public function cRuleProvider()
	{
		return [
			# C as initial sound before A, H, K, L, O, Q, R, U, X = '4'
			[ 'CA', '4' ],
			[ 'CH', '4' ],
			[ 'CK', '4' ],
			[ 'CL', '45' ],
			[ 'CO', '4' ],
			[ 'CQ', '4' ],
			[ 'CR', '47' ],
			[ 'CU', '4' ],
			[ 'CX', '48' ],

			# Ca as initial sound NOT before A, H, K, L, O, Q, R, U, X = '8'
			[ 'CB', '81' ],
			[ 'CC', '8' ],
			[ 'CD', '82' ],
			[ 'CE', '8' ],
			[ 'CF', '83' ],
			[ 'CG', '84' ],
			[ 'CI', '8' ],
			[ 'CJ', '8' ],
			[ 'CM', '86' ],
			[ 'CN', '86' ],
			[ 'CP', '81' ],
			[ 'CS', '8' ],
			[ 'CT', '82' ],
			[ 'CV', '83' ],
			[ 'CW', '83' ],
			[ 'CY', '8' ],
			[ 'CZ', '8' ],

			# C after S, Z = '8'
			[ 'SC', '8' ],
			[ 'ZC', '8' ],

			# C before A, H, K, O, Q, U, X but NOT after S, Z = '4'
			[ 'BCA', '14' ],
			[ 'BCH', '14' ],
			[ 'BCK', '14' ],
			[ 'BCO', '14' ],
			[ 'BCQ', '14' ],
			[ 'BCU', '14' ],
			[ 'BCX', '148' ],
		];
	}
}
