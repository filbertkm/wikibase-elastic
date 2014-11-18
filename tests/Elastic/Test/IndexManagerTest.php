<?php

namespace Wikibase\Elastic\Test;

use Elastica\Document;
use Elastica\Index;
use Wikibase\Elastic\Index\IndexManager;

class IndexManagerTest extends TestCase {

	public function testBuild() {
		$indexName = 'testindex';

		$indexManager = $this->getIndexManager( $indexName );
		$indexManager->build();

		$index = $this->getIndex( $indexName );

		$this->assertEquals( $this->getExpectedMapping(), $index->getMapping() );
		$this->assertEquals( 1, $index->count() );

		$index->delete();
	}

	private function getIndexManager( $indexName ) {
		$index = $this->getIndex( $indexName );

		return new IndexManager(
			$index,
			$this->getIndexer( $index ),
			$this->getMappingBuilder()
		);
	}

	private function getIndexer( Index $index ) {
		$indexer = $this->getMock( 'Wikibase\Elastic\Index\Indexer\Indexer' );

		$indexer->expects( $this->any() )
			->method( 'doIndex' )
			->will( $this->returnCallback( function() use ( $index ) {
				$data = array(
					'label' => 'omg a kitten!'
				);

				$document = new Document( 'kittens', $data, 'terms' );
				$document->setDocAsUpsert( true );

				$index->updateDocuments( array( $document ) );
				$index->refresh();
			} ) );

		return $indexer;
	}

	private function getMappingBuilder() {
		$mappingBuilder = $this->getMock( 'Wikibase\Elastic\Index\Mapping\MappingBuilder' );
		$mapping = $this->getTestMapping();

		$mappingBuilder->expects( $this->any() )
			->method( 'build' )
			->will( $this->returnValue( $mapping ) );

		return $mappingBuilder;
	}

	private function getTestMapping() {
		return array(
			'terms' => $this->getMappingProperties()
		);
	}

	private function getMappingProperties() {
		return array(
			'label' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			)
		);
	}

	private function getExpectedMapping() {
		return array(
			'terms' => array(
				'properties' => $this->getMappingProperties()
			)
		);
	}

}
