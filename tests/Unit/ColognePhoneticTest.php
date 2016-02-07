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

	/**
	 * @return array
	 */
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
	public function testSpecialRulesForC( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	/**
	 * @return array
	 */
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
			[ 'SCX', '8' ],
			[ 'ZCX', '8' ],

			# C before A, H, K, O, Q, U, X but NOT after S, Z = '4'
			[ 'BCA', '14' ],
			[ 'BCH', '14' ],
			[ 'BCK', '14' ],
			[ 'BCO', '14' ],
			[ 'BCQ', '14' ],
			[ 'BCU', '14' ],
			[ 'BCX', '148' ],

			# C not before A, H, K, O, Q, U, X = '8'
			[ 'BCB', '181' ],
			[ 'BCC', '18' ],
			[ 'BCD', '182' ],
			[ 'BCE', '18' ],
			[ 'BCF', '183' ],
			[ 'BCG', '184' ],
			[ 'BCI', '18' ],
			[ 'BCJ', '18' ],
			[ 'BCL', '185' ],
			[ 'BCM', '186' ],
			[ 'BCN', '186' ],
			[ 'BCP', '181' ],
			[ 'BCR', '187' ],
			[ 'BCS', '18' ],
			[ 'BCT', '182' ],
			[ 'BCV', '183' ],
			[ 'BCW', '183' ],
			[ 'BCY', '18' ],
			[ 'BCZ', '18' ],

		];
	}

	/**
	 * @param string $word
	 * @param string $expectedCode
	 *
	 * @dataProvider dtRuleProvider
	 */
	public function testSpecialRulesForDT( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	/**
	 * @return array
	 */
	public function dtRuleProvider()
	{
		return [
			# D not before C, S, Z = '2'
			[ 'DA', '2' ],
			[ 'DB', '21' ],
			[ 'DD', '2' ],
			[ 'DE', '2' ],
			[ 'DF', '23' ],
			[ 'DF', '23' ],
			[ 'DG', '24' ],
			[ 'DH', '2' ],
			[ 'DI', '2' ],
			[ 'DJ', '2' ],
			[ 'DK', '24' ],
			[ 'DL', '25' ],
			[ 'DM', '26' ],
			[ 'DN', '26' ],
			[ 'DO', '2' ],
			[ 'DP', '21' ],
			[ 'DQ', '24' ],
			[ 'DR', '27' ],
			[ 'DT', '2' ],
			[ 'DU', '2' ],
			[ 'DV', '23' ],
			[ 'DW', '23' ],
			[ 'DX', '248' ],
			[ 'DY', '2' ],

			# T not before C, S, Z = '2'
			[ 'TA', '2' ],
			[ 'TB', '21' ],
			[ 'TD', '2' ],
			[ 'TE', '2' ],
			[ 'TF', '23' ],
			[ 'TF', '23' ],
			[ 'TG', '24' ],
			[ 'TH', '2' ],
			[ 'TI', '2' ],
			[ 'TJ', '2' ],
			[ 'TK', '24' ],
			[ 'TL', '25' ],
			[ 'TM', '26' ],
			[ 'TN', '26' ],
			[ 'TO', '2' ],
			[ 'TP', '21' ],
			[ 'TQ', '24' ],
			[ 'TR', '27' ],
			[ 'TT', '2' ],
			[ 'TU', '2' ],
			[ 'TV', '23' ],
			[ 'TW', '23' ],
			[ 'TX', '248' ],
			[ 'TY', '2' ],

			# D before C, S, Z = '8'
			[ 'DC', '8' ],
			[ 'DS', '8' ],
			[ 'DZ', '8' ],

			# T before C, S, Z = '8'
			[ 'TC', '8' ],
			[ 'TS', '8' ],
			[ 'TZ', '8' ],
		];
	}

	/**
	 * @param string $word
	 * @param string $expectedCode
	 *
	 * @dataProvider pRuleProvider
	 */
	public function testSpecialRulesForP( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	/**
	 * @return array
	 */
	public function pRuleProvider()
	{
		return [
			# P not before H = '1'
			[ 'PA', '1' ],
			[ 'PB', '1' ],
			[ 'PC', '18' ],
			[ 'PD', '12' ],
			[ 'PE', '1' ],
			[ 'PF', '13' ],
			[ 'PG', '14' ],
			[ 'PG', '14' ],
			[ 'PI', '1' ],
			[ 'PJ', '1' ],
			[ 'PK', '14' ],
			[ 'PL', '15' ],
			[ 'PM', '16' ],
			[ 'PN', '16' ],
			[ 'PO', '1' ],
			[ 'PP', '1' ],
			[ 'PQ', '14' ],
			[ 'PR', '17' ],
			[ 'PS', '18' ],
			[ 'PT', '12' ],
			[ 'PU', '1' ],
			[ 'PV', '13' ],
			[ 'PW', '13' ],
			[ 'PX', '148' ],
			[ 'PY', '1' ],
			[ 'PZ', '18' ],

			# P before H = '3'
			[ 'PH', '3' ],
		];
	}

	/**
	 * @param string $word
	 * @param string $expectedCode
	 *
	 * @dataProvider xRuleProvider
	 */
	public function testSpecialRulesForX( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	/**
	 * @return array
	 */
	public function xRuleProvider()
	{
		return [
			# X not after C, K, Q = '48'
			[ 'AX', '48' ],
			[ 'BX', '148' ],
			[ 'DX', '248' ],
			[ 'EX', '48' ],
			[ 'FX', '348' ],
			[ 'GX', '48' ],
			[ 'HX', '48' ],
			[ 'IX', '048' ],
			[ 'JX', '048' ],
			[ 'LX', '548' ],
			[ 'MX', '648' ],
			[ 'NX', '648' ],
			[ 'OX', '048' ],
			[ 'PX', '148' ],
			[ 'RX', '748' ],
			[ 'SX', '848' ],
			[ 'TX', '248' ],
			[ 'UX', '048' ],
			[ 'VX', '348' ],
			[ 'WX', '348' ],
			[ 'XX', '4848' ],
			[ 'YX', '048' ],
			[ 'ZX', '848' ],

			# X after C, K, Q = '8'
			[ 'CX', '48' ],
			[ 'KX', '48' ],
			[ 'QX', '48' ],
		];
	}

	public function testEmptyWordReturnsEmptyIndex()
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( '' );

		$this->assertEquals( '', $index );
	}

	public function testEmptyPhraseReturnsEmptyArray()
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getPhraseIndex( '' );

		$this->assertEquals( [ ], $index );
	}

	/**
	 * @param string $word
	 * @param string $expectedCode
	 *
	 * @dataProvider wordsProvider
	 */
	public function testSomeWords( $word, $expectedCode )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getWordIndex( $word );

		$this->assertEquals( $expectedCode, $index );
	}

	/**
	 * @return array
	 */
	public function wordsProvider()
	{
		return [
			[ 'Wikipedia', '3412' ],
			[ 'Holger', '547' ],
			[ 'Woltersdorf', '35278273' ],
		];
	}

	/**
	 * @param string $phrase
	 * @param array  $expectedArray
	 *
	 * @dataProvider phrasesProvider
	 */
	public function testSomePhrases( $phrase, $expectedArray )
	{
		$colognePhonetic = new ColognePhonetic();

		$index = $colognePhonetic->getPhraseIndex( $phrase );

		$this->assertEquals( $expectedArray, $index );
	}

	/**
	 * @return array
	 */
	public function phrasesProvider()
	{
		return [
			[
				'Ein Satz mit vielen Wörtern',
				[
					'Ein'     => '06',
					'Satz'    => '8',
					'mit'     => '62',
					'vielen'  => '356',
					'Wortern' => '37276',
				],
			],
		];
	}
}
