<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once ( "$IP/maintenance/Maintenance.php" );

class ItemQueryer extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Query Wikibase";
		$this->addArg( "property", "property id" );
		$this->addArg( "item", "item id" );
	}

	public function execute() {
		$property = $this->getArg( 0 );
		$item = $this->getArg( 1 );

		$data = array(
			'claims.property' => strtolower( $property ),
			'claims.value' => strtolower( $item )
		);

		$query = new \Wikibase\Elastic\Query\NestedQuery();
		$results = $query->query( 'claims', $data );

		if ( $results->count() > 0 ) {
			foreach( $results as $result ) {
				$hit = $result->getHit();
				$source = $result->getSource();

				$labelText = '';

				foreach( $source['labels'] as $label ) {
					if ( $label['lang'] === 'en' ) {
						$labelText = $label['label'];
					}
				}

				echo $hit['_id'] . ' - ' . $labelText . "\n";
			}
		} else {
			echo "no results found\n";
		}
	}
}

$maintClass = "ItemQueryer";
require_once RUN_MAINTENANCE_IF_MAIN;
