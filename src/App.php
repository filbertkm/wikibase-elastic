<?php

namespace Wikibase\Elastic;

use DataValues\Deserializers\DataValueDeserializer;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Elastica\Client;
use Elastica\Index;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\Elastic\Connection;
use Wikibase\Elastic\Dump\EntityDumpLookup;
use Wikibase\Elastic\Dump\EntityIdDumpPager;
use Wikibase\Elastic\Index\Indexer\EntityBatchIndexer;
use Wikibase\Elastic\Index\Indexer\EntityIndexer;
use Wikibase\Elastic\Index\Indexer\StatementDocumentBuilder;
use Wikibase\Elastic\Logger;
use Wikibase\InternalSerialization\DeserializerFactory;
use Wikibase\Repo\Store\SQL\EntityPerPageIdPager;
use Wikibase\Repo\WikibaseRepo;
use Wikibase\Utils;

class App {

	private $wikibaseRepo;

	/**
	 * @return Client
	 */
	public function getClient() {
		$conn = new Connection( $GLOBALS['wgCirrusSearchServers'] );
		return $conn->getClient();
	}

	/**
	 * @param Index $index
	 *
	 * @return EntityIndexer
	 */
	public function getEntityIndexer( Index $index ) {
		$batchIndexer = new EntityBatchIndexer(
			$index,
			new StatementDocumentBuilder( $this->getWikibaseRepo()->getPropertyDataTypeLookup() ),
			$this->getWikibaseRepo()->getEntityLookup(),
			new Logger(),
			Utils::getLanguageCodes()
		);

		return new EntityIndexer(
			new EntityPerPageIdPager(
				$this->getWikibaseRepo()->getStore()->newEntityPerPage()
			),
			$batchIndexer
		);
	}

	/**
	 * @return WikibaseRepo
	 */
	public function getWikibaseRepo() {
		if ( !isset( $this->wikibaseRepo ) ) {
			$this->wikibaseRepo = WikibaseRepo::getDefaultInstance();
		}

		return $this->wikibaseRepo;
	}

	/**
	 * @return App
	 */
	public static function getDefaultInstance() {
		return new self();
	}

}
