<?php

namespace Wikibase\Elastic\Index\Indexer;

use Elastica\Document;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\PropertyDataTypeLookup;
use Wikibase\DataModel\Statement\Statement;

class StatementDocumentBuilder {

	private $propertyDataTypeLookup;

	public function __construct( PropertyDataTypeLookup $propertyDataTypeLookup ) {
		$this->propertyDataTypeLookup = $propertyDataTypeLookup;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return Document
	 */
	public function getDocument( Entity $entity, Statement $statement ) {
		$data = $this->buildStatementData( $entity, $statement );

		$typeName = 'statement';
		$documentId = $this->getDocumentId( $entity, $statement );
		$document = new Document( $documentId, $data, $typeName );
		$document->setDocAsUpsert( true );

		return $document;
	}

	private function getDocumentId( Entity $entity, Statement $statement ) {
		$documentId = $entity->getId()->getSerialization();
		$documentId .= '_' . $statement->getHash();

		return $documentId;
	}

	/**
	 * @param Entity $entity
	 * @param Statement $statement
	 *
	 * @return array
	 */
	private function buildStatementData( Entity $entity, Statement $statement ) {
		$mainSnak = $statement->getClaim()->getMainSnak();
		$propertyId = $mainSnak->getPropertyId();
		$snakType = $mainSnak->getType();

		$data = array(
			'entity_id' => $entity->getId()->getSerialization(),
			'property_id' => $propertyId->getSerialization(),
			'snak_type' => $snakType,
			'data_type' => $this->propertyDataTypeLookup->getDataTypeIdForProperty( $propertyId ),
			'hash' => $statement->getHash()
		);

		if ( $snakType === 'value' ) {
			$value = $mainSnak->getDataValue();

			if ( $value->getType() === 'string' ) {
				$data['value_string'] = $value->getValue();
			} else if ( $value->getType() === 'wikibase-entityid' ) {
				$data['value_string'] = $value->getEntityId()->getSerialization();
			}
		}

		return $data;
	}

}
