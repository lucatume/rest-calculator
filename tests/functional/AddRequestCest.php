<?php
namespace Tests\Functional;
class AddRequestCest {
	/**
	 * It should store last operation result
	 * @test
	 *
	 * @example [5,4]
	 * @example [5,0]
	 * @example [5,-5]
	 * @example [5,-8]
	 * @example [0,0]
	 * @example [-1,-3]
	 */
	public function return_the_correct_result( \FunctionalTester $I, \Codeception\Example $example ) {
		$first  = $example[0];
		$second = $example[1];

		$I->amOnPage( "/wp-json/calc/add/{$first}/{$second}" );

		$I->seeResponseCodeIs( 200 );
		$I->seeOptionInDatabase( [ 'option_name' => '_transient_last_operation' ] );
	}
}
