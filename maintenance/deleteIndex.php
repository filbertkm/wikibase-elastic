<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class IndexDeleter extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->mDescription = "Delete index";
		$this->addArg( 'index-name', 'Index name' );
	}

	public function execute() {
		$indexName = $this->getArg( 0 );

		if ( !$indexName ) {
			throw new Exception( "Error: index-name not provided\n" );
		}

		$conn = new \Wikibase\Elastic\Connection();
		$index = $conn::getClient()->getIndex( $indexName );

		try {
			$index->delete();
		} catch ( \Elastica\Exception\ResponseException $ex ) {
			$this->output( "$indexName not found\n" );
			return;
		}
		$this->output( "$indexName deleted\n" );
	}
}

$maintClass = "IndexDeleter";
require_once RUN_MAINTENANCE_IF_MAIN;
