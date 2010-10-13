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
		
		if (empty($dir) || !is_dir(dir)) {
			$dir = sys_get_temp_dir();
		}

		if (empty($suffix)) {
			$result = tempnam($dir, $prefix);
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
	 * @return numeric|boolean Number of bytes saved into a lock file on success, false on failure
	 */
	public static function lock($lock, $debug = false) {
		$result = false;

		$proc_pid = getmypid();

		if (file_exists($lock)) {
			$file_pid = file_get_contents($lock);
			if ($file_pid <> $proc_pid) {
				$file_pid_proc = trim(shell_exec("ps -p $file_pid -o comm="));
				if (!empty($file_pid_proc)) {
					self::debug("Found old lock. Process $file_pid_proc [PID=$file_pid] is still running.", $debug);
				}    
				else {
					self::debug("Found old lock.  Cleaning up.", $debug);
					$result = file_put_contents($lock, $proc_pid);
				}    
			}    
			else {
				self::debug("Lock already in place.", $debug);
			}    
		}    
		else {
			self::debug("Locking", $debug);
			$result = file_put_contents($lock, $proc_pid);
		}    

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
				self::debug("Removed lock file $lock", $debug);
			}
			else {
				self::debug("Failed to remove lock file $lock", $debug);
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
			print $message;
		}
	}
}

?>
