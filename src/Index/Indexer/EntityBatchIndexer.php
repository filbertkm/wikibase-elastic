<?php

namespace Wikibase\Elastic\Index\Indexer;

use Elastica\Document;
use Elastica\Index;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\Elastic\Index\DocumentBuilder\StatementDocumentBuilder;
use Wikibase\Elastic\Index\DocumentBuilder\TermsDocumentBuilder;
use Wikibase\Elastic\Logger;
use Wikibase\Lib\Store\EntityLookup;

class EntityBatchIndexer {

	/**
	 * @var Index
	 */
	private $index;

	/**
	 * @var StatementDocumentBuilder
	 */
	private $statementDocumentBuilder;

	/**
	 * @var EntityLookup
	 */
	private $entityLookup;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var string[]
	 */
	private $languageCodes;

	/**
	 * @param Index $index
	 * @param StatementDocumentBuilder $statementDocumentBuilder
	 * @param EntityLookup $entityLookup
	 * @param Logger $logger
	 * @param string[] $languageCodes
	 */
	public function __construct( Index $index, StatementDocumentBuilder $statementDocumentBuilder,
		EntityLookup $entityLookup, Logger $logger, array $languageCodes
	) {
		$this->index = $index;
		$this->statementDocumentBuilder = $statementDocumentBuilder;
		$this->entityLookup = $entityLookup;
		$this->logger = $logger;
		$this->languageCodes = $languageCodes;
	}

	/**
	 * @param EntityId[] $entityIds
	 */
	public function indexBatch( array $entityIds ) {
		foreach( $entityIds as $entityId ) {
			try {
				$entity = $this->entityLookup->getEntity( $entityId );

				if ( $entity ) {
					$this->indexEntity( $entity );
				} else {
					$this->logger->log( $entityId->getSerialization() . ' not found' );
				}
			} catch ( \Exception $ex ) {
				$this->logger->log( 'Skipped ' . $entityId->getSerialization() .
					' due to error: ' . $ex->getMessage() );
			}
		}

		$this->logger->log( 'processed up to ' . $entityId->getSerialization() );
	}

	private function indexEntity( Entity $entity ) {
		$documents = array();

		foreach( $this->languageCodes as $languageCode ) {
			$termsDocumentBuilder = new TermsDocumentBuilder( $languageCode );
			$documents[] = $termsDocumentBuilder->getDocument( $entity );
		}

		foreach( $entity->getStatements() as $statement ) {
			$documents[] = $this->statementDocumentBuilder->getDocument( $entity, $statement );
		}

		$this->index->updateDocuments( $documents );

		$this->index->refresh();
	}

}
