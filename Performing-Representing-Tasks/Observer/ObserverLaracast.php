<?php

interface Observer {
	public function handle();
}


interface Subject {
	public function attach( Observer $observer );

	public function detach( Observer $observer );

	public function notify();
}



class Login implements Subject {

	protected $observers = [];

	public function attach( Observer $observer ) {
		if(is_array( $observer )){
			foreach ($observer as $ob){
				$this->attach( $ob );
			}

			return;
		}

		$this->observers[] = $observer;
		return $this;
	}

	public function detach( Observer $observer ) {
		foreach ( $this->observers as $key => $ob ) {
			if ( $ob == $this->observers[ $key ] ) {
				unset( $this->observers[ $key ] );
			}
		}
	}


	public function notify() {
		foreach ( $this->observers as $ob ) {
			$ob->notify();
		}
	}
}

class LoginHandler implements Observer {

	public function handle() {
		var_dump( 'Logg ' );
	}

}
class EmailHandler implements Observer {

	public function handle() {
		var_dump( 'Logg ' );
	}

}


$login = new Login();
$login->attach( [
	new LoginHandler(),
	new EmailHandler()
] );














