<?php


interface SubjectInterface {
	public function attachObserver( ObserverInterface $observer );

	public function detach( ObserverInterface $observer );

	public function notify(EventInterface $event);
}

interface ObserverInterface {
	public function update( EventInterface $subject );
}

interface EventInterface{
	public function getName();
	public function getSender();
}

class FootbalTeam implements SubjectInterface {

	private $observers = [];
	private $name;


	public function __construct( $name ) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function attachObserver( ObserverInterface $observer ) {
		$this->observers[] = $observer;
	}

	public function detach( ObserverInterface $observer ) {
		foreach ( $this->observers as $key => $obs ) {
			if ( $obs === $observer ) {
				unset( $this->observers[ $key ] );

				return;
			}
		}
	}

	public function notify(EventInterface $event) {
		foreach ( $this->observers as $obs ) {
			$obs->update( $this );
		}
	}

}

class FootballEvent implements EventInterface{

	const GOAL = 'Goal';
	const GOAL_ENEMY = 'Fuck NOOO!';
	const FIGHT = 'Fight';

	private $name;
	private $sender;

	/**
	 * FootballEvent constructor.
	 *
	 * @param $name
	 * @param $sender
	 */
	public function __construct( $name, $sender ) {
		$this->name   = $name;
		$this->sender = $sender;
	}

	public function getName() {
		return $this->name;
	}

	public function getSender() {
		$this->sender;
	}
}

class FootballFan implements ObserverInterface {

	private $name;

	public function __construct( $name ) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function update( EventInterface $event ) {
		echo $event->getName() . " reacted to {$event->getSender()->getName()}! \n";
	}
}


$team = new FootbalTeam( 'Dinamo' );
$fan1 = new FootballFan('Varketili');
$fan2 = new FootballFan('Gldani');

$team->attachObserver( $fan1 );
$team->attachObserver( $fan2 );


//$team->notify();

$team->detach( $fan1 );
$team->notify();