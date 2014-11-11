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

class WikibaseItemIndexer extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Index Wikibase items";
	}

	public function execute() {
		$app = App::getDefaultInstance();
		$index = $app->getClient()->getIndex( wfWikiId() . '_wikibase' );

		$indexManager = new IndexManager(
			$index,
			$app->getEntityIndexer( $index ),
			Utils::getLanguageCodes()
		);

		$indexManager->build();
	}
}

$maintClass = "WikibaseItemIndexer";
require_once RUN_MAINTENANCE_IF_MAIN;
