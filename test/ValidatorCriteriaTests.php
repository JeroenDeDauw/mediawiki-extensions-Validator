<?php

/**
 * Unit tests for Validators criteria.
 * 
 * @ingroup Validator
 * @since 0.4.8
 * 
 * @licence GNU GPL v3
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValidatorCriteriaTests extends MediaWikiTestCase {
	
	/**
	 * Tests CriterionHasLength.
	 */
	public function testCriterionHasLength() {
		$tests = array(
			array( true, 0, 5, 'foo' ),
			array( false, 0, 5, 'foobar' ),
			array( false, 3, null, 'a' ),
			array( true, 3, null, 'aw<dfxdfwdxgtdfgdfhfdgsfdxgtffds' ),
			array( true, null, null, 'aw<dfxdfwdxgtdfgdfhfdgsfdxgtffds' ),
			array( true, null, null, '' ),
			array( false, 2, 3, '' ),
			array( true, 3, false, 'foo' ),
			array( false, 3, false, 'foobar' ),
		);
		
		foreach ( $tests as $test ) {
			$c = new CriterionHasLength( $test[1], $test[2] );
			$p = new Parameter( 'test' );
			$p->setUserValue( 'test', $test[3] );
			$this->assertEquals(
				$test[0],
				$c->validate( $p, array() )->isValid(),
				'Lenght of value "'. $test[3] . '" should ' . ( $test[0] ? '' : 'not ' ) . "be between $test[1] and $test[2] ."
			);
		}
	}
	
	/**
	 * Tests CriterionInArray.
	 */
	public function testCriterionInArray() {
		$tests = array(
			array( true, 'foo', false, array( 'foo', 'bar', 'baz' ) ),
			array( true, 'FoO', false, array( 'fOo', 'bar', 'baz' ) ),
			array( false, 'FoO', true, array( 'fOo', 'bar', 'baz' ) ),
			array( false, 'foobar', false, array( 'foo', 'bar', 'baz' ) ),
			array( false, '', false, array( 'foo', 'bar', 'baz' ) ),
			array( false, '', false, array( 'foo', 'bar', 'baz', 0 ) ),
		);
		
		foreach ( $tests as $test ) {
			$c = new CriterionInArray( $test[3], $test[2] );
			$p = new Parameter( 'test' );
			$p->setUserValue( 'test', $test[1] );
			$this->assertEquals(
				$test[0],
				$c->validate( $p, array() )->isValid(),
				'Value "'. $test[1] . '" should ' . ( $test[0] ? '' : 'not ' ) . "be in list '" . $GLOBALS['wgLang']->listToText( $test[3] ) . "'."
			);
		}
	}
	
	/**
	 * Tests CriterionInRange.
	 */
	public function testCriterionInRange() {
		$tests = array(
			array( true, '42', Parameter::TYPE_INTEGER, 0, 99 ),
			array( false, '42', Parameter::TYPE_INTEGER, 0, 9 ),
			array( true, '42', Parameter::TYPE_INTEGER, 0, false ),
			array( true, '42', Parameter::TYPE_INTEGER, false, false ),
			array( false, '42', Parameter::TYPE_INTEGER, false, 9 ),
			array( false, '42', Parameter::TYPE_INTEGER, 99, false ),
			array( false, '42', Parameter::TYPE_INTEGER, 99, 100 ),
			array( true, '42', Parameter::TYPE_INTEGER, 42, 42 ),
			array( false, '4.2', Parameter::TYPE_FLOAT, 42, 42 ),
			array( true, '4.2', Parameter::TYPE_FLOAT, 4.2, 4.2 ),
			array( true, '4.2', Parameter::TYPE_FLOAT, 0, 9 ),
			array( true, '42', Parameter::TYPE_FLOAT, 0, 99 ),
			array( false, '42', Parameter::TYPE_FLOAT, 0, 9 ),
			array( true, '-42', Parameter::TYPE_INTEGER, false, 99 ),
			array( true, '-42', Parameter::TYPE_INTEGER, -99, false ),
			array( true, '42', Parameter::TYPE_INTEGER, -99, false ),
		);
		
		foreach ( $tests as $test ) {
			$c = new CriterionInRange( $test[3], $test[4] );
			$p = new Parameter( 'test', $test[2] );
			$p->setUserValue( 'test', $test[1] );
			$this->assertEquals(
				$test[0],
				$c->validate( $p, array() )->isValid(),
				'Value "'. $test[1] . '" should ' . ( $test[0] ? '' : 'not ' ) . "be between '$test[3]' and '$test[4]'."
			);
		}
	}
	
	/**
	 * Tests CriterionIsFloat.
	 */
	public function testCriterionIsFloat() {
		$tests = array(
			array( true, '42' ),
			array( true, '4.2' ),
			array( false, '4.2.' ),
			array( false, '42.' ),
			array( false, '4a2' ),
			array( true, '-42' ),
			array( true, '-4.2' ),
			array( false, '' ),
			array( true, '0' ),
			array( true, '0.0' ),
		);
		
		foreach ( $tests as $test ) {
			$c = new CriterionIsFloat();
			$p = new Parameter( 'test' );
			$p->setUserValue( 'test', $test[1] );
			$this->assertEquals(
				$test[0],
				$c->validate( $p, array() )->isValid(),
				'Value "'. $test[1] . '" should ' . ( $test[0] ? '' : 'not ' ) . "be a float."
			);
		}
	}
	
	/**
	 * Tests CriterionIsInteger.
	 */
	public function testCriterionIsInteger() {
		$tests = array(
			array( true, '42', true ),
			array( false, '4.2', true ),
			array( false, '4.2.', true ),
			array( false, '42.', true ),
			array( false, '4a2', true ),
			array( true, '-42', true ),
			array( false, '-42', false ),
			array( false, '-4.2', true ),
			array( false, '', true ),
			array( true, '0', true ),
		);
		
		foreach ( $tests as $test ) {
			$c = new CriterionIsInteger( $test[2] );
			$p = new Parameter( 'test' );
			$p->setUserValue( 'test', $test[1] );
			$this->assertEquals(
				$test[0],
				$c->validate( $p, array() )->isValid(),
				'Value "'. $test[1] . '" should ' . ( $test[0] ? '' : 'not ' ) . "be an integer."
			);
		}
	}
	
}