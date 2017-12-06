<?php

abstract class Tile {
	abstract public function getWealthFactor(): int;
}


class Plains extends Tile {

	public $wealthFactor = 2;

	public function getWealthFactor(): int {
		$this->wealthFactor;
	}
}

abstract class TileDecorator extends Tile {
	protected $tile;

	public function __construct( Tile $tile ) {
		$this->tile = $tile;
	}
}

class DiamondDecorator extends TileDecorator {

	public function getWealthFactor(): int {
		return $this->tile->getWealthFactor() + 2;
	}
}

class PollutionDecorator extends TileDecorator {
	public function getWealthFactor(): int {
		return $this->tile->getWealthFactor() - 4;
	}
}


$tile = new Plains();
print $tile->getWealthFactor(); // 2

$tile = new DiamondDecorator( new Plains() );
print $tile->getWealthFactor(); // 4

$tile = new PollutionDecorator( new DiamondDecorator( new Plains() ) );
print $tile->getWealthFactor(); // 0















