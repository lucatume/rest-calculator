<?php


class HashCest {

	/**
	 * It should return the correct hash
	 *
	 * @test
	 */
	public function it_should_return_the_correct_hash(FunctionalTester $I) {
		$I->amOnPage("/wp-json/calc/hash/aba");

		$I->seeResponseCodeIs(200);

		$I->see(restCalculator_hash('aba'));
	}
}
