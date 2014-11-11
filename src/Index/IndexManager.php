<?php

namespace Wikibase\Elastic\Index;

use Elastica\Index;
use Elastica\Type\Mapping;
use Wikibase\Elastic\Index\Indexer\Indexer;
use Wikibase\Elastic\Index\Mapping\IndexMappingBuilder;

class IndexManager {

	/**
	 * @var Index
	 */
	private $index;

	/**
	 * @var Indexer
	 */
	private $indexer;

	/**
	 * @var IndexMappingBuilder
	 */
	private $indexMappingBuilder;

	/**
	 * @param Index $index
	 * @param Indexer $indexer
	 * @param string[] $languageCodes
	 */
	public function __construct(
		Index $index,
		Indexer $indexer,
		array $languageCodes
	) {
		$this->index = $index;
		$this->indexer = $indexer;

		$this->mappingBuilder = new IndexMappingBuilder( $languageCodes );
	}

	public function build() {
		// deletes and recreates if index exists
		$this->index->create( $this->buildIndexConfig(), true );

		$this->createMappings( $this->mappingBuilder->build() );

		$this->indexer->doIndex();
	}

	/**
	 * @return array
	 */
	private function buildIndexConfig() {
		$config = array(
			'settings' => array(
				'number_of_replicas' => 0
			)
		);

		return $config;
	}

	/**
	 * @param array $mappingConfigs
	 */
	private function createMappings( array $mappingConfigs ) {
		foreach( $mappingConfigs as $typeName => $fields ) {
			$this->createMapping( $typeName, $fields );
		}
	}

	/**
	 * @param Index $index
	 * @param string $typeName
	 * @param array $fields
	 */
	private function createMapping( $typeName, array $fields ) {
		$elasticaType = $this->index->getType( $typeName );

		$mapping = new Mapping();
		$mapping->setType( $elasticaType );

		$mapping->setProperties( $fields );

		$mapping->send();
	}

}