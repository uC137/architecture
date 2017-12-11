<?php


class Login implements \SplSubject {
	private $storage;

// ...
	public function __construct() {
		$this->storage = new \SplObjectStorage();
	}


	public function attach( \SplObserver $observer ) {
		$this->storage->attach( $observer );
	}

	public function detach( \SplObserver $observer ) {
		$this->storage->detach( $observer );
	}

	public function notify() {
		foreach ( $this->storage as $obs ) {
			$obs->update( $this );
		}
	}


}


abstract class LoginObserver implements \SplObserver {
	private $login;

	public function __construct( Login $login ) {
		$this->login = $login;
		$login->attach( $this );
	}

	public function update( \SplSubject $subject ) {
		if ( $subject === $this->login ) {
			$this->doUpdate( $subject );
		}

	}

	abstract public function doUpdate( Login $login );

}







