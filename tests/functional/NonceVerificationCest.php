<?php


class NonceVerificationCest {

	public function _before( FunctionalTester $I ) {
	}

	public function _after( FunctionalTester $I ) {
	}

	/**
	 * It should allow identifying yourself
	 *
	 * @test
	 */
	public function it_should_allow_identifying_yourself( FunctionalTester $I ) {
		$id = $I->haveUserInDatabase( 'luca', [ 'user_pass' => 'luca' ] );

		// we cannot use wp_signon here as it requires a redirection
		$I->loginAs( 'luca', 'luca' );

		// set up the recipes for the nonce
		$_COOKIE[ LOGGED_IN_COOKIE ] = $I->grabCookie( LOGGED_IN_COOKIE );
		wp_set_current_user( $id );
		$restNonce = wp_create_nonce( 'wp_rest' );

		$I->haveHttpHeader( 'X-WP-Nonce', $restNonce );
		$I->amOnPage( "/wp-json/calc/whoami" );

		$I->seeResponseCodeIs( 200 );
		$I->see( 'luca' );
	}
}
