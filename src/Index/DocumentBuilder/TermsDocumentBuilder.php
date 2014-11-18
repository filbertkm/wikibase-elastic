<?php

namespace Wikibase\Elastic\Index\DocumentBuilder;

use Elastica\Document;
use Wikibase\DataModel\Entity\Entity;

class TermsDocumentBuilder {

	/**
	 * @var string
	 */
	private $languageCode;

	/**
	 * @param string $languageCode
	 */
	public function __construct( $languageCode ) {
		$this->languageCode = $languageCode;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return Document
	 */
	public function getDocument( Entity $entity ) {
		$data = $this->buildTermsData( $entity );

		$typeName = 'terms_' . $this->languageCode;
		$document = new Document( $entity->getId()->getSerialization(), $data, $typeName );
		$document->setDocAsUpsert( true );

		return $document;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return array
	 */
	private function buildTermsData( Entity $entity ) {
		$data = array(
			'id' => $entity->getId()->getSerialization()
		);

		if ( $label = $entity->getLabel( $this->languageCode ) ) {
			$data['label'] = $label;
		}

		if ( $description = $entity->getDescription( $this->languageCode ) ) {
			$data['description'] = $description;
		}

		return $data;
	}

}
