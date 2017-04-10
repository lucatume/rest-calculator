<?php

namespace Tests\Unit;

use Operand;

class OperandTest extends \Codeception\Test\Unit {
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	public function notAnInt() {
		return [
			[ 'foo' ],
			[ (object) [ 'foo' => 'bar' ] ],
			[ 234234.234 ],
			[ 1.3 ]
		];
	}

	/**
	 * It should throw if value is not an int
	 * @test
	 *
	 * @dataProvider notAnInt
	 */
	public function throw_if_value_is_not_an_int() {
		$this->expectException( \InvalidArgumentException::class );

		new Operand( 'foo' );
	}

	/**
	 * It should support positive integer values
	 * @test
	 */
	public function support_positive_integer_values() {
		new Operand( 23 );
	}

	/**
	 * It should support negative integer values
	 * @test
	 */
	public function support_negative_integer_values() {
		new Operand( - 23 );
	}

	/**
	 * It should support zero value
	 * @test
	 */
	public function support_zero_value() {
		new Operand( 0 );
	}
}