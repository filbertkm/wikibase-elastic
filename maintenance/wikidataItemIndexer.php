<?php

use Wikibase\Elastic\App;
use Wikibase\Elastic\Index\IndexManager;
use Wikibase\Utils;

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . '/../config/mappings.php';

class WikidataItemIndexer extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Index Wikidata items";
	}

	public function execute() {
        $app = App::getDefaultInstance();
		$index = $app->getClient()->getIndex( 'wikidata' );

        $indexManager = new IndexManager(
            $index,
            $app->getEntityDumpIndexer( $index ),
			Utils::getLanguageCodes()
        );

        $indexManager->build();
	}
}

$maintClass = "WikidataItemIndexer";
require_once RUN_MAINTENANCE_IF_MAIN;
