<?php

require_once dirname(__FILE__) . '/locker.class.php';

$lockFile = Locker::getName();
$lock = Locker::lock($lockFile);

if ($lock) {
	print "Process is locked now.\n";
	$sameLock = Locker::lock($lockFile);
	if ($sameLock) {
		print "Something is very wrong - locked same process twice.\n";
	}
	else {
		print "Faield to get a second lock on the same process.\n";
		print "Unlocking\n";
		$unlock = Locker::unlock($lockFile);
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
