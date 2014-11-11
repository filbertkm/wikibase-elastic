<?php

namespace Wikibase\Elastic\Index\Mapping;

class IndexMappingBuilder {

	private $termsMappingBuilder;

	public function __construct( array $languageCodes ) {
		$this->termsMappingBuilder = new TermsMappingBuilder( $languageCodes );
	}

	public function build() {
		return $this->termsMappingBuilder->build();
	}

}
