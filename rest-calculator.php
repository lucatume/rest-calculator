<?php
/*
Plugin Name: REST Calculator
Description: Add numbers using WP REST API!
Version: 0.1.0
Author: Luca Tumedei
Author URI: http://theaveragedev.com
*/

if (function_exists('add_action')) {
	add_action('rest_api_init', function () {
		register_rest_route('calc', 'add/(?P<o1>[-\.\d]+)/(?P<o2>[-\.\d]+)', [
			'methods' => 'GET',
			'callback' => [new Calculator, 'add'],
		]);

		register_rest_route('calc', 'times/(?P<o1>[-\.\d]+)/(?P<o2>[-\.\d]+)', [
			'methods' => 'GET',
			'callback' => [new Calculator, 'times'],
		]);

		register_rest_route('calc', 'hash/(?P<input>.*)', [
			'methods' => 'GET',
			'callback' => [new Calculator, 'hash'],
		]);
	});
}

class Calculator {

	public function add(WP_REST_Request $req) {
		return $this->process($req, 'add');
	}

	protected function process(WP_REST_Request $req, $op = 'add') {
		try {
			$operand_1 = new Operand($req->get_param('o1'));
			$operand_2 = new Operand($req->get_param('o2'));
		} catch (InvalidArgumentException $e) {
			$response = new WP_REST_Response('Bad operands');
			$response->set_status(400);

			return $response;
		}

		switch ($op) {
		default:
		case 'add':
			$operation = new Addition($operand_1, $operand_2);
			break;
		case 'times':
			$operation = new Division($operand_1, $operand_2);
			break;
		}

		$value = $operation->get_value();

		set_transient('last_operation', $operation . ' with result ' . $value);

		$response = new WP_REST_Response($value);
		$response->set_status(200);

		return $response;
	}

	public function times(WP_REST_Request $req) {
		if (get_current_user_id() == 0 || !current_user_can('read')) {
			$response = new WP_REST_Response('Divisions are for subscribers only');
			$response->set_status(403);

			return $response;
		}

		return $this->process($req, 'times');
	}

	public function hash(WP_REST_Request $req) {
		$value = $req->get_param('input');

		return restCalculator_hash($value);
	}
}

class Operand {

	private $value;

	function __construct($value) {
		if (!(filter_var($value, FILTER_VALIDATE_INT) || '0' == $value)) {
			throw new InvalidArgumentException('Not an int');
		}

		$this->value = (int)$value;
	}

	function get_value() {
		return $this->value;
	}
}

class Operation {

	protected $o1;

	protected $o2;

	function __construct(Operand $o1, Operand $o2) {
		$this->o1 = $o1;
		$this->o2 = $o2;
	}
}

class Addition extends Operation {

	function __toString() {
		$v1 = $this->o1->get_value();
		$v2 = $this->o2->get_value();
		$line_1 = 'Add: ' . $v1 . '+' . $v2 . '=' . $this->get_value();
		$line_2 = trailingslashit("Route: /wp-json/calc/add/{$v1}/{$v2}");

		return $line_1 . "\n" . $line_2;
	}

	function get_value() {
		return $this->o1->get_value() + $this->o2->get_value();
	}
}

class Division extends Operation {

	function __toString() {
		$v1 = $this->o1->get_value();
		$v2 = $this->o2->get_value();
		$line_1 = 'Divide: ' . $v1 . '/' . $v2 . '=' . $this->get_value();
		$line_2 = trailingslashit("Route: /wp-json/calc/times/{$v1}/{$v2}");

		return $line_1 . "\n" . $line_2;
	}

	function get_value() {
		if ($this->o2->get_value() == 0) {
			return 'really?!';
		}

		return $this->o1->get_value() / $this->o2->get_value();
	}
}

function restCalculator_hash($value) {
	$hash = md5(ucwords(str_replace('a', '4', $value)));

	return apply_filters('restCalculator_hash', $hash);
}
