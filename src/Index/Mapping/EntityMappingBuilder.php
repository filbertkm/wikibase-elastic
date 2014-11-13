<?php

namespace Wikibase\Elastic\Index\Mapping;

class EntityMappingBuilder {

	private $termsMappingBuilder;

	private $statementMappingBuilder;

	public function __construct( array $languageCodes ) {
		$this->termsMappingBuilder = new TermsMappingBuilder( $languageCodes );
		$this->statementMappingBuilder = new StatementMappingBuilder();
	}

	/**
	 * @return array
	 */
	public function build() {
		return array(
			$this->termsMappingBuilder->build(),
			$this->statementMappingBuilder->build()
		);
	}

}
