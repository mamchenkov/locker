<?php

require_once dirname(__FILE__) . '/../locker.class.php';

class LockerTest extends PHPUnit_Framework_TestCase {

	public function test__getName__default() {
		$lock = Locker::getName();
		$this->assertTrue(strlen($lock) > 0);
	}

	public function test__getName__unique() {
		$lock_1 = Locker::getName();
		$lock_2 = Locker::getName();
		$this->assertFalse($lock_1 == $lock_2);
	}

	public function test__getName__suffix() {
		$lock_1 = Locker::getName();
		$this->assertEquals(0, preg_match('/foobar/', $lock_1));
		$lock_2 = Locker::getName('foobar');
		$this->assertEquals(1, preg_match('/foobar/', $lock_2));
	}

	public function test__getName__prefix() {
		$lock_1 = Locker::getName(null);
		$this->assertEquals(0, preg_match('/foobar/', $lock_1));
		$lock_2 = Locker::getName(null, 'foobar');
		$this->assertEquals(1, preg_match('/foobar/', $lock_2));
	}


	public function test__lock() {
		$lockFile = Locker::getName();
		$lock = Locker::lock($lockFile);
		$this->assertTrue($lock > 0);

		$lock = Locker::lock($lockFile);
		$this->assertFalse($lock);
	}

	public function test__unlock() {
		$lockFile = Locker::getName();
		$lock = Locker::lock($lockFile);
		$unlock = Locker::unlock($lockFile);

		$this->assertTrue($unlock);
	}

	public function test__debug() {
		$this->markTestIncomplete();
	}
}

?>
