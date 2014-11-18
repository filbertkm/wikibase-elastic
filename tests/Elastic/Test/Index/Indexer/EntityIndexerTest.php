<?php

namespace Wikibase\Elastic\Test\Index\Indexer;

use Elastica\Document;
use Elastica\Type\Mapping;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Elastic\Index\Indexer\EntityIndexer;
use Wikibase\Elastic\Test\TestCase;

class EntityIndexerTest extends TestCase {

	public function testDoIndex() {
		$entityIds = array(
			new ItemId( 'Q1' ),
			new ItemId( 'Q2' ),
			new ItemId( 'Q3' ),
			new ItemId( 'Q4' ),
			new ItemId( 'Q5' )
		);

		$entityIndexer = new EntityIndexer(
			new EntityArrayPager( $entityIds ),
			$this->getEntityBatchIndexer(),
			2
		);

		$entityIndexer->doIndex();

		$index = $this->getIndex( 'test_entityindexer' );
		$this->assertEquals( 5, $index->count() );
	}

	private function getEntityBatchIndexer() {
		$entityBatchIndexer = $this->getMockBuilder( 'Wikibase\Elastic\Index\Indexer\EntityBatchIndexer' )
			->disableOriginalConstructor()
			->getMock();

		$index = $this->newIndex( 'test_entityindexer' );

		$entityBatchIndexer->expects( $this->any() )
			->method( 'indexBatch' )
			->will( $this->returnCallback( function( array $entityIds ) use( $index ) {
				$mapping = new Mapping();
				$mapping->setType( $index->getType( 'entities' ) );
				$mapping->setProperties( array( 'entity_id' => array( 'type' => 'string' ) ) );
				$mapping->send();

				foreach( $entityIds as $entityId ) {
					$prefixedId = $entityId->getSerialization();
	                $data = array(
	                    'entity_id' => $prefixedId
	                );

    	            $document = new Document( $prefixedId, $data, 'entities' );
					$document->setDocAsUpsert( true );

					$index->updateDocuments( array( $document ) );
					$index->refresh();
				}
			} ) );

		return $entityBatchIndexer;
	}

}
