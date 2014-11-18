<?php

namespace Wikibase\Elastic\Index\Mapping;

class EntityMappingBuilder implements MappingBuilder {

	/**
	 * @var TermsMappingBuilder
	 */
	private $termsMappingBuilder;

	/**
	 * @var StatementMappingBuilder
	 */
	private $statementMappingBuilder;

	/**
	 * @param string[] $languageCodes
	 */
	public function __construct( array $languageCodes ) {
		$this->termsMappingBuilder = new TermsMappingBuilder( $languageCodes );
		$this->statementMappingBuilder = new StatementMappingBuilder();
	}

	/**
	 * @return array
	 */
	public function build() {
		return $this->termsMappingBuilder->build()
			 + $this->statementMappingBuilder->build();
	}

}
