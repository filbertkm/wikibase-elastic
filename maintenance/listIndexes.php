<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class IndexLister extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "List indexes";
	}

	public function execute() {
		$conn = new \Wikibase\Elastic\Connection();
		$indexNames = $conn::getClient()->getStatus()->getIndexNames();

		foreach( $indexNames as $indexName ) {
			echo "* $indexName \n";
		}
	}
}

$maintClass = "IndexLister";
require_once RUN_MAINTENANCE_IF_MAIN;
