<?php
namespace Tests\Acceptance;

use AcceptanceTester;

class AddRequestCest {
	/**
	 * It should return the correct result
	 * @test
	 *
	 * @example [5,4,9]
	 * @example [5,0,5]
	 * @example [5,-5,0]
	 * @example [5,-8,-3]
	 * @example [0,0,0]
	 * @example [-1,-3,-4]
	 */
	public function return_the_correct_result( AcceptanceTester $I , \Codeception\Example $example ) {
		$first = $example[0];
		$second = $example[1];
		$expected = $example[2];

		$I->sendGET( "/calc/add/{$first}/{$second}" );

		$I->seeResponseEquals( $expected );
	}
}
