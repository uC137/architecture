<?php


interface SubjectInterface {
	public function attachObserver( ObserverInterface $observer );

	public function detachObserver( ObserverInterface $observer );

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

	public function detachObserver( ObserverInterface $observer ) {
		foreach ( $this->observers as $key => $obs ) {
			if ( $obs === $observer ) {
				unset( $this->observers[ $key ] );

				return;
			}
		}
	}

	public function notify(EventInterface $event) {
		foreach ( $this->observers as $obs ) {
			$obs->update( $event );
		}
	}


	public function goalAction() {
		$event = new FootballEvent( FootballEvent::GOAL , $this);
		$this->notify($event);
	}
	public function goalEnemyAction() {
		$event = new FootballEvent( FootballEvent::GOAL_ENEMY , $this);
		$this->notify($event);
	}
	public function fightAction() {
		$event = new FootballEvent( FootballEvent::FIGHT , $this);
		$this->notify($event);
	}
}

class FootballEvent implements EventInterface {

	const GOAL = 'Goal! ';
	const GOAL_ENEMY = 'Fuck NOOO! ';
	const FIGHT = 'Fight ';

	private $name;
	private $sender;

	/**
	 * FootballEvent constructor.
	 *
	 * @param $name
	 * @param $sender
	 */
	public function __construct( $name, FootbalTeam $sender ) {
		$this->name   = $name;
		$this->sender = $sender;
	}

	public function getName() {
		return $this->name;
	}

	public function getSender() {
		return $this->sender;
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

		switch ($event->getName()){
			case FootballEvent::GOAL:
					echo "GOAAAAAL ! {$this->getName()}";
				break;
			case FootballEvent::GOAL_ENEMY:
					echo "Oh NOOO ! {$this->getName()}";
				break;
			case FootballEvent::FIGHT:
					echo "GO boys ! {$this->getName()}";
				break;
		}


		echo $event->getName() . " reacted to {$event->getSender()->getName()}! \n";
	}
}


$team = new FootbalTeam( 'Dinamo' );
$fan1 = new FootballFan('GoderZI ');
$fan2 = new FootballFan('MAimuni Lawiraki ');

$team->attachObserver( $fan1 );
$team->attachObserver( $fan2 );

$team->goalAction();

$team->detachObserver( $fan1 );
$team->goalEnemyAction();

$fan3 = new FootballFan('Gary Tapor ');
$team->attachObserver( $fan3 );
$team->fightAction();
