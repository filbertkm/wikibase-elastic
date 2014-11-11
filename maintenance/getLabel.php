<?php

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Elastic\App;

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class LabelLookup extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Query Wikibase";
	}

	public function execute() {
		$index = App::getDefaultInstance()->getClient()->getIndex( 'testrepo_wikibase' );
		$termLookup = new \Wikibase\Elastic\Lookup\ElasticTermLookup( $index );
		$labels = $termLookup->getLabels( new ItemId( 'Q116' ) );

		var_export( $labels );
	}
}

$maintClass = "LabelLookup";
require_once RUN_MAINTENANCE_IF_MAIN;
