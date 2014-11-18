<?php

namespace Wikibase\Elastic\Index;

use Elastica\Index;
use Elastica\Type\Mapping;
use Wikibase\Elastic\Index\Indexer\Indexer;
use Wikibase\Elastic\Index\Mapping\MappingBuilder;

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
	 * @var MappingBuilder
	 */
	private $mappingBuilder;

	/**
	 * @param Index $index
	 * @param Indexer $indexer
	 * @param MappingBuilder $mappingBuilder
	 */
	public function __construct( Index $index, Indexer $indexer, MappingBuilder $mappingBuilder ) {
		$this->index = $index;
		$this->indexer = $indexer;
		$this->mappingBuilder = $mappingBuilder;
	}

	public function build() {
		// deletes and recreates if index exists
		$this->index->create( $this->buildIndexConfig(), true );

		foreach( $this->mappingBuilder->build() as $mappingConfig ) {
			$this->createMapping( $mappingConfig );
		}

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
	private function createMapping( array $mappingConfig ) {
		foreach( $mappingConfig as $typeName => $fields ) {
			$this->createMappingForType( $typeName, $fields );
		}
	}

	/**
	 * @param string $typeName
	 * @param array $fields
	 */
	private function createMappingForType( $typeName, array $fields ) {
		$elasticaType = $this->index->getType( $typeName );

		$mapping = new Mapping();
		$mapping->setType( $elasticaType );

		$mapping->setProperties( $fields );

		$mapping->send();
	}

}
