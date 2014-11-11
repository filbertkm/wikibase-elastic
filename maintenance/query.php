<?php

use Wikibase\Elastic\Connection;

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

		$path = 'wikidatawiki/term/_search';

		$data = array(
			'query' => array(
				'filtered' => array(
					'filter' => array(
						'term' => array(
							'terms.lang' => $lang
						)
					)
				)
			)
		);

		$conn = new Wikibase\Elastic\Connection();
		$client = $conn::getClient();

		$result = $client->request(
			$path,
			Elastica\Request::GET,
			$data,
			array()
		);
	}
}

$maintClass = "Queryer";
require_once RUN_MAINTENANCE_IF_MAIN;
