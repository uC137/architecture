<?php


// Let’s imagine that the internals of this code are more complicated than they actually are,
// and that I am stuck with using it rather than rewriting it from scratch.

function getProductFileLines( $file ) {
	return file( $file );
}

function getProductObjectFromId( $id, $productname ) {
	// some kind of database lookup
	return new Product( $id, $productname );
}

function getNameFromLine( $line ) {
	if ( preg_match( "/.*-(.*)\s\d+/", $line, $array ) ) {
		return str_replace( '_', ' ', $array[1] );
	}

	return '';
}

function getIDFromLine( $line ) {
	if ( preg_match( "/^(\d{1,3})-/", $line, $array ) ) {
		return $array[1];
	}

	return - 1;
}

class Product {
	public $id;
	public $name;

	public function __construct( $id, $name ) {
		$this->id   = $id;
		$this->name = $name;
	}
}


$lines   = getProductFileLines( __DIR__ . '/test2.txt' );
$objects = [];
foreach ( $lines as $line ) {
	$id             = getIDFromLine( $line );
	$name           = getNameFromLine( $line );
	$objects[ $id ] = getProductObjectFromID( $id, $name );
}


//Implementation

class ProductFacade {
	private $products = [];

	public function __construct( string $file ) {
		$this->file = $file;
		$this->compile();
	}

	private function compile() {
		$lines = getProductFileLines( $this->file );
		foreach ( $lines as $line ) {
			$id                    = getIDFromLine( $line );
			$name                  = getNameFromLine( $line );
			$this->products[ $id ] = getProductObjectFromID( $id, $name );
		}
	}

	public function getProducts(): array {
		return $this->products;
	}

	public function getProduct( string $id ): \Product {
		if ( isset( $this->products[ $id ] ) ) {
			return $this->products[ $id ];
		}

		return null;
	}
}

//From the point of view of the client code, access to Product objects from a log file is much simplified:
$facade = new ProductFacade(__DIR__ . '/test2.txt');
$object = $facade->getProduct("234");

