<?php

namespace Wikibase\Elastic\Index\Indexer;

use Wikibase\Store\EntityIdPager;

class EntityIndexer implements Indexer {

	/**
	 * @var EntityIdPager
	 */
	private $entityIdPager;

	/**
	 * @var EntityBatchIndexer
	 */
	private $entityBatchIndexer;

	/**
	 * @var int
	 */
	private $batchSize;

	/**
	 * @param EntityIdPager $entityIdPager
	 * @param EntityBatchIndexer $entityBatchIndexer
	 * @param int $batchSize Default: 100
	 */
	public function __construct(
		EntityIdPager $entityIdPager,
		EntityBatchIndexer $entityBatchIndexer,
		$batchSize = 100
	) {
		$this->entityIdPager = $entityIdPager;
		$this->entityBatchIndexer = $entityBatchIndexer;
		$this->batchSize = $batchSize;
	}

	public function doIndex() {
		while ( $entityIds = $this->entityIdPager->fetchIds( $this->batchSize ) ) {
			$this->entityBatchIndexer->indexBatch( $entityIds );
		}
	}

}
