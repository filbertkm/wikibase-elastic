<?php

namespace Wikibase\Elastic\Index\Mapping;

class TermsMappingBuilder implements MappingBuilder {

	/**
	 * @var string[]
	 */
	private $languageCodes;

	/**
	 * @param string[] $languageCodes
	 */
	public function __construct( array $languageCodes ) {
		$this->languageCodes = $languageCodes;
	}

	public function build() {
		$mapping = array();

		foreach( $this->languageCodes as $languageCode ) {
			$typeName = 'terms_' . $languageCode;
			$properties = $this->getProperties();

			$mapping[$typeName] = $properties;
		}

		return $mapping;
	}

	private function getProperties() {
		return array(
			'id' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'label' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'description' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			)
		);
	}

}
