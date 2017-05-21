<?php

namespace Test\Functional;

use FunctionalTester;

class TimesRequestCest {

	/**
	 * It should return 403 if user is not subscriber
	 *
	 * @test
	 */
	public function it_should_return_403_if_user_is_not_subscriber(FunctionalTester $I) {
		$I->amOnPage("/wp-json/calc/times/4/2");

		$I->seeResponseCodeIs(403);
	}

	/**
	 * It should return the result when the user is a subscriber
	 *
	 * @test
	 */
	public function it_should_return_the_result_when_the_user_is_a_subscriber(FunctionalTester $I) {
		$id = $I->factory()->user->create(['user_login' => 'user', 'role' => 'subscriber', 'user_pass' => 'secret']);

		$I->loginAs('user', 'secret');
		$I->extractCookie(LOGGED_IN_COOKIE);
		wp_set_current_user($id);

		$I->haveHttpHeader('X-WP-Nonce', wp_create_nonce('wp_rest'));
		$I->amOnPage("/wp-json/calc/times/4/2");

		$I->seeResponseCodeIs(200);
		$I->see('2');
	}
}
