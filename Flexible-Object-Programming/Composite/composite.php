<?php


//abstract class Unit
//{
//	abstract public function bombardStrength(): int;
//}
//
//class Archer extends Unit
//{
//	public function bombardStrength(): int
//	{
//		return 4;
//	}
//}
//
//class LaserCannonUnit extends Unit
//{
//	public function bombardStrength(): int
//	{
//		return 44;
//	}
//}
//
//class Army
//{
//	public $armies = [];
//	private $units = [];
//	public function addUnit(Unit $unit)
//	{
//		array_push($this->units, $unit);
//	}
//
//	public function addArmy(Army $army)
//	{
//		array_push($this->armies, $army);
//	}
//
//	public function bombardStrength(): int
//	{
//		$ret = 0;
//		foreach ($this->units as $unit) {
//			$ret += $unit->bombardStrength();
//		}
//
//		foreach ($this->armies as $army) {
//			$ret += $army->bombardStrength();
//		}
//
//		return $ret;
//	}
//}
//
//
//// listing 10.03
//$unit1 = new Archer();
//$unit2 = new LaserCannonUnit();
//$army = new Army();
//$army->addUnit($unit1);
//$army->addUnit($unit2);
//print $army->bombardStrength();

#---------------------------Implement Composite patterns----------------------


abstract class Unit {
	public function getComposite() {
		return null;
	}

	abstract public function bombardStrength(): int;
}


abstract class CompositeUnit extends Unit {
	private $units = [];

	public function getComposite(): CompositeUnit {
		return $this;
	}

	public function addUnit( Unit $unit ) {
		if ( in_array( $unit, $this->units, true ) ) {
			return;
		}
		$this->units[] = $unit;
	}

	public function removeUnit( Unit $unit ) {
		$idx = array_search( $unit, $this->units, true );
		if ( is_int( $idx ) ) {
			array_splice( $this->units, $idx, 1, [] );
		}
	}

	public function getUnits(): array {
		return $this->units;
	}
}


class Army extends CompositeUnit {

	private $units = [];


	public function bombardStrength(): int {
		$ret = 0;
		foreach ( $this->units as $unit ) {
			$ret += $unit->bombardStrength();
		}

		return $ret;
	}
}

class Archer extends Unit {
	//I do not want to make it possible to add a Unit object to an Archer object,
	// so I throw exceptions if addUnit() or removeUnit() are called.

	public function bombardStrength(): int {
		return 4;
	}
}

class LaserCannonUnit extends Unit {

	public function bombardStrength(): int {
		return 44;
	}
}
class Cavalry extends Unit {

	public function bombardStrength(): int {
		return 1;
	}
}

class TroopCarrier extends CompositeUnit {
	public function addUnit( Unit $unit ) {
		if ( $unit instanceof Cavalry ) {
			throw new UnitException( "Can't get a horse on the vehicle" );
		}
		parent::addUnit( $unit );
	}

	public function bombardStrength(): int {
		return 0;
	}
}

class UnitScript {

	public static function joinExisting( Unit $newUnit, Unit $occupyingUnit ): CompositeUnit {
		$comp = $occupyingUnit->getComposite();
		if ( ! is_null( $comp ) ) {
			$comp->addUnit( $newUnit );
		} else {
			$comp = new Army();
			$comp->addUnit( $occupyingUnit );
			$comp->addUnit( $newUnit );
		}

		return $comp;
	}
}


// create an army
$main_army = new Army();
// add some units
$main_army->addUnit( new Archer() );
$main_army->addUnit( new LaserCannonUnit() );
// create a new army
$sub_army = new Army();
// add some units
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );
// add the second army to the first
$main_army->addUnit( $sub_army );
// all the calculations handled behind the scenes
print "attacking with strength: {$main_army->bombardStrength()}\n";

// Pros of Composit pattern
//Flexibility: Because everything in the Composite pattern shares a common
//supertype, it is very easy to add new composite or leaf objects to the design without
//changing a program’s wider context.

//Simplicity: A client using a Composite structure has a straightforward interface.
//There is no need for a client to distinguish between an object that is composed
//of others and a leaf object (except when adding new components). A call to
//Army::bombardStrength() may cause a cascade of delegated calls behind the
//scenes; but to the client, the process and result are exactly equivalent to those
//associated with calling Archer::bombardStrength().

//Implicit reach: Objects in the Composite pattern are organized in a tree. Each
//composite holds references to its children. An operation on a particular part of the
//tree, therefore, can have a wide effect. We might remove a single Army object from its
//Army parent and add it to another. This simple act is wrought on one object, but it has
//the effect of changing the status of the Army object’s referenced Unit objects and of
//their own children.

//Explicit reach: Tree structures are easy to traverse. They can be iterated in order
//to gain information or to perform transformations. We will look at a particularly
//powerful technique for this in the next chapter when we deal with the Visitor pattern.