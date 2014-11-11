<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class Queryer extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Query Wikibase";
		$this->addArg( "lang", "language" );
		$this->addArg( "label", "label" );
	}

	public function execute() {
		$lang = $this->getArg( 0 );
		$label = $this->getArg( 1 );

		$data = array(
			'terms.lang' => $lang
		);

		$termQuery = new \Wikibase\Elastic\Query\TermQuery();
		$results = $termQuery->query( $data );

		if ( $results->count() > 0 ) {
			foreach( $results as $result ) {
				$hit = $result->getHit();
				echo $hit['_id'] . "\n";
			}
		} else {
			echo "no results found\n";
		}
	}
}

$maintClass = "Queryer";
require_once RUN_MAINTENANCE_IF_MAIN;
