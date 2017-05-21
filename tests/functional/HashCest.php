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

	/**
	 * It should return the filtered hash value
	 *
	 * @test
	 */
	public function it_should_return_the_filtered_hash_value(FunctionalTester $I) {
		add_filter('restCalculator_hash', function($value){
			return $value . 'foo';
		});

		$I->amOnPage("/wp-json/calc/hash/aba");

		$I->seeResponseCodeIs(200);

		$I->see(restCalculator_hash('aba'));
	}
}
