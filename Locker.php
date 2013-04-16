<?php
/**
 * Locker class
 *
 * This class provides easy way of locking/unlocking for a process not to
 * step on its own toes.
 *
 * @author Leonid Mamchenkov <leonid@mamchenkov.net>
 * @package libs
 * @subpackage locker
 */
class Locker {

	const DEFAULT_PREFIX = 'locker_';

	/**
	 * Generate a random file name (full path)
	 *
	 * @param string $suffix Suffix for the file name to use (otherwise random)
	 * @param string $prefix Prefix for the file name to use
	 * @param string $dir Directory to use for path
	 * @return string
	 */
	public static function getName($suffix = null, $prefix = self::DEFAULT_PREFIX, $dir = null) {
		$result = '';
		
		if (empty($dir) || !is_dir($dir)) {
			$dir = sys_get_temp_dir();
		}

		if (empty($suffix)) {
			$result = tempnam($dir, $prefix); // tempnam() actually creates the file
		}
		else {
			$result = $dir . '/' . $prefix . $suffix;
		}


		return $result;
	}

	/**
	 * Lock 
	 *
	 * @param string $lock Path to lock file
	 * @param boolean $debug Whether to enable debug output
	 * @return integer|boolean Number of bytes saved into a lock file on success, false on failure
	 */
	public static function lock($lock, $debug = false) {
		$result = false;

		$proc_pid = getmypid();

		$lockedPid = self::getLockedPid($lock, $debug);
		$pidAlive = self::isPidAlive($lockedPid, $debug);

		if (!$pidAlive) {
			$result = self::writeLock($lock, $proc_pid, $debug);
		}
		return $result;
	}

	/**
	 * Find the PID from the lock file
	 *
	 * @param string $lock Lock file name
	 * @param boolean $debug Debug on/off
	 * @return null|integer
	 */
	private static function getLockedPid($lock, $debug) {
		$result = null;

		if (!file_exists($lock)) {
			self::debug("Lock file [$lock] does not exist", $debug);
			return $result;
		}

		$file_pid = file_get_contents($lock);
		if ($file_pid && is_numeric($file_pid)) {
			$result = $file_pid;
		}
		else {
			self::debug("Lock file [$lock] does not contain valid PID", $debug);
		}

		return $result;
	}

	/**
	 * Check if given PID is still running
	 *
	 * @param integer $pid PID to check
	 * @param boolean $debug Debug on/off
	 * @return boolean True if alive, false otherwise
	 */
	private static function isPidAlive($pid, $debug) {
		$result = false;

		if ($pid && is_numeric($pid)) {
			$pid_proc = trim(shell_exec("ps -p $pid -o comm="));
			if (!empty($pid_proc)) {
				self::debug("PID [$pid] is alive and running [$pid_proc]", $debug);
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Write lock file
	 *
	 * @param string $lock Name of the lock file
	 * @param integer $proc_pid Process ID to write to lock file
	 * @param boolean $debug Debug on/off
	 * @return integer Number of bytes written to lock file
	 */
	private static function writeLock($lock, $proc_pid, $debug) {
		self::debug("Writing lock file [$lock] for PID [$proc_pid]", $debug);
		$result = file_put_contents($lock, $proc_pid);
		return $result;
	}

	/**
	 * Unlock
	 *
	 * @param string $lock Path to lock file
	 * @param boolean $debug Whether to enable debug output or not
	 * @return boolean True if success, false if failer
	 */
	public static function unlock($lock, $debug = false) {
		$result = false;

		if (file_exists($lock)) {
			$result = unlink($lock);
			if ($result) {
				self::debug("Removed lock file [$lock]", $debug);
			}
			else {
				self::debug("Failed to remove lock file [$lock]", $debug);
			}
		}

		return $result;
	}

	/**
	 * Print debug message
	 *
	 * @param string $message Message to print
	 * @param boolean $debug True if printing needed, false otherwise
	 */
	private static function debug($message, $debug) {
		if ($message && $debug) {
			print "$message\n";
		}
	}
}

?>
