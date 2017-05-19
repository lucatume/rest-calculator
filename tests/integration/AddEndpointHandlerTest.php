<?php
namespace Tests\Integration;
class AddEndpointHandlerTest extends \Codeception\TestCase\WPTestCase {
	public function inputsAndOutputs() {
		return [
			[ 5, 4, 9 ],
			[ 5, 0, 5 ],
			[ 5, - 5, 0 ],
			[ 5, - 8, - 3 ],
			[ 0, 0, 0 ],
			[ - 1, - 3, -4 ]
		];
	}

	/**
	 * It should return the correct result
	 * @test
	 *
	 * @dataProvider inputsAndOutputs
	 */
	public function return_the_correct_result( $o1, $o2, $expected ) {
		$request = new \WP_REST_Request();
		$request->set_param( 'o1', $o1 );
		$request->set_param( 'o2', $o2 );

		$module = new \Calculator();
		$result = $module->add( $request );

		$this->assertInstanceOf( \WP_REST_Response::class, $result );
		$this->assertEquals( $expected, $result->data );
	}
}
