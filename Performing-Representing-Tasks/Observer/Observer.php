<?php

//Problem
// The Login class soon becomes very tightly embedded into this particular system.
class LoginB {
	const LOGIN_USER_UNKNOWN = 1;
	const LOGIN_WRONG_PASS = 2;
	const LOGIN_ACCESS = 3;
	private $status = [];

	public function handleLogin( string $user, string $pass, string $ip ): bool {
		$isvalid = false;
		switch ( rand( 1, 3 ) ) {

			case 1:
				$this->setStatus( self::LOGIN_ACCESS, $user, $ip );
				$isvalid = true;
				break;
			case 2:
				$this->setStatus( self::LOGIN_WRONG_PASS, $user, $ip );
				$isvalid = false;
				break;
			case 3:
				$this->setStatus( self::LOGIN_USER_UNKNOWN, $user, $ip );
				$isvalid = false;
				break;
				print "returning " . ( ( $isvalid ) ? "true" : "false" ) . "\n";

		}

		//Logger::logIP($user, $ip, $this->getStatus());
		return $isvalid;
	}

	private function setStatus( int $status, string $user, string $ip ) {
		$this->status = [ $status, $user, $ip ];
	}

	public function getStatus(): array {
		return $this->status;
	}
}


//Implementation
//At the core of the Observer pattern is the unhooking of client elements (the observers) from a central class
//(the subject).Observers need to be informed when events occur that the subject knows about.
//At the same time, you do not want the subject to have a hardcoded relationship with its observer classes.

interface Observable {
	public function attach( Observer $observer );

	public function detach( Observer $observer );

	public function notify();
}

interface Observer {
	public function update( Observable $observable );
}


class Login implements Observable {

	private $observers = [];
	private $storage;
	const LOGIN_USER_UNKNOWN = 1;
	const LOGIN_WRONG_PASS = 2;
	const LOGIN_ACCESS = 3;


	public function attach( Observer $observer ) {
		$this->observers[] = $observer;

	}

	public function detach( Observer $observer ) {
		$this->observers = array_filter( $this->observers,
			function ( $a ) use ( $observer ) {
				return ( ! ( $a === $observer ) );
			}
		);

	}

	public function notify() {
		foreach ( $this->observers as $jobs ) {
			$jobs->update( $this );
		}

	}


}


class LoginAnalytics implements Observer {
	public function update( Observable $observable ) {
		// not type safe!
		$status = $observable->getStatus();
		print __CLASS__ . ": doing something with status info\n";
	}
}


abstract class LoginObserver implements Observer {
	private $login;

	public function __construct( Login $login ) {
		$this->login = $login;
		$login->attach( $this );
	}

	public function update( Observable $observable ) {
		if ( $observable === $this->login ) {
			$this->doUpdate( $observable );
		}
	}

	abstract public function doUpdate( Login $login );
}


class SecurityMonitor extends LoginObserver {
	public function doUpdate( Login $login ) {
		$status = $login->getStatus();
		if ( $status[0] == Login::LOGIN_WRONG_PASS ) {
			// send mail to sysadmin
			print __CLASS__ . ": sending mail to sysadmin\n";
		}
	}
}

class GeneralLogger extends LoginObserver {
	public function doUpdate( Login $login ) {
		$status = $login->getStatus();
		// add login data to log
		print __CLASS__ . ": add login data to log\n";
	}
}


class PartnershipTool extends LoginObserver {
	public function doUpdate( Login $login ) {
		$status = $login->getStatus();
		// check $ip address
		// set cookie if it matches a list
		print __CLASS__ . ": set cookie if it matches a list\n";
	}
}

$login = new Login();
new SecurityMonitor($login);
new GeneralLogger($login);
new PartnershipTool($login);










