<?php
/*
Plugin Name: REST Calculator
Description: Add numbers using WP REST API!
Version: 0.1.0
Author: Luca Tumedei
Author URI: http://theaveragedev.com
*/

if ( function_exists( 'add_action' ) ) {
	add_action( 'rest_api_init', function () {
		register_rest_route( 'calc', 'add/(?P<o1>[-\.\d]+)/(?P<o2>[-\.\d]+)', [
			'methods'  => 'GET',
			'callback' => [ new Calculator, 'process' ]
		] );
		register_rest_route( 'calc', 'whoami', [
			'methods'  => 'GET',
			'callback' => 'restcalculator_whoami'
		] );
	} );
}

function restcalculator_whoami() {
	echo(wp_get_current_user()->user_login);
	die();
}
class Calculator {
	function process( WP_REST_Request $req ) {
		try {
			$operand_1 = new Operand( $req->get_param( 'o1' ) );
			$operand_2 = new Operand( $req->get_param( 'o2' ) );
		} catch ( InvalidArgumentException $e ) {
			$response = new WP_REST_Response( 'Bad operands' );
			$response->set_status( 400 );

			return $response;
		}

		$operation = new Addition( $operand_1, $operand_2 );
		$value     = $operation->get_value();

		set_transient( 'last_operation', $operation . ' with result ' . $value );

		$response = new WP_REST_Response( $value );
		$response->set_status( 200 );

		return $response;
	}
}

class Operand {
	private $value;

	function __construct( $value ) {
		if ( ! ( filter_var( $value, FILTER_VALIDATE_INT ) || '0' == $value ) ) {
			throw new InvalidArgumentException( 'Not an int' );
		}

		$this->value = (int) $value;
	}

	function get_value() {
		return $this->value;
	}
}

class Addition {
	private $o1;
	private $o2;

	function __construct( Operand $o1, Operand $o2 ) {
		$this->o1 = $o1;
		$this->o2 = $o2;
	}

	function __toString() {
		$v1     = $this->o1->get_value();
		$v2     = $this->o2->get_value();
		$line_1 = 'Add: ' . $v1 . '+' . $v2 . '=' . $this->get_value();
		$line_2 = trailingslashit( "Route: /wp-json/calc/add/{$v1}/{$v2}" );

		return $line_1 . "\n" . $line_2;
	}

	function get_value() {
		return $this->o1->get_value() + $this->o2->get_value();
	}
}
