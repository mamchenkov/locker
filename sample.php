<?php

require_once dirname(__FILE__) . '/Locker.php';

$debug = true;
$lockFile = Locker::getName();
$lock = Locker::lock($lockFile, $debug);

if ($lock) {
	print "Process is locked now.\n";
	$sameLock = Locker::lock($lockFile, $debug);
	if ($sameLock) {
		print "Something is very wrong - locked same process twice.\n";
	}
	else {
		print "Faield to get a second lock on the same process.\n";
		print "Unlocking\n";
		$unlock = Locker::unlock($lockFile, $debug);
		if ($unlock) {
			print "Unlocked\n";
		}
		else {
			print "Failed to unlock\n";
		}
	}
}
else {
	print "Failed to lock process.\n";
}

?>
