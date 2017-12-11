<?php


abstract class Question {
	protected $prompt;
	protected $marker;

	public function __construct( string $prompt, Marker $marker ) {
		$this->prompt = $prompt;
		$this->marker = $marker;
	}

	public function mark( string $response ): bool {
		return $this->marker->mark( $response );
	}
}

class TextQuestion extends Question {
// do text question specific things
}

// listing 11.15
class AVQuestion extends Question {
// do audiovisual question specific things
}


abstract class Marker {
	protected $test;

	public function __construct( string $test ) {
		$this->test = $test;
	}

	abstract public function mark( string $response ): bool;
}

class MarkLogicMarker extends Marker {
	private $engine;

	public function __construct( string $test ) {
		parent::__construct( $test );
		$this->engine = new MarkParse( $test );
	}

	public function mark( string $response ): bool {
		return $this->engine->evaluate( $response );
	}
}

class MatchMarker extends Marker {
	public function mark( string $response ): bool {
		return ( $this->test == $response );
	}
}

class RegexpMarker extends Marker {
	public function mark( string $response ): bool {
		return ( preg_match( "$this->test", $response ) === 1 );
	}
}


$markers = [
	new RegexpMarker( "/f.ve/" ),
	new MatchMarker( "five" ),
	new MarkLogicMarker( '$input equals "five"' )
];
foreach ( $markers as $marker ) {
	print get_class( $marker ) . "\n";
	$question = new TextQuestion( "how many beans make five", $marker );
}

foreach ( [ "five", "four" ] as $response ) {
	print " response: $response: ";
	if ( $question->mark( $response ) ) {
		print "well done\n";
	} else {
		print "never mind\n";
	}
}


//In the example, I passed specific data (the $response variable) from the client to the strategy object
//via the mark() method. Sometimes, you may encounter circumstances in which you don’t always know
//in advance how much information the strategy object will require when its operation is invoked. You can
//delegate the decision as to what data to acquire by passing the strategy an instance of the client itself. The
//strategy can then query the client in order to build the data it needs.
