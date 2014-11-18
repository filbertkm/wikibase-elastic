<?php

namespace Wikibase\Elastic\Test\Index\Indexer;

use Wikibase\Repo\Store\EntityIdPager;

class EntityArrayPager implements EntityIdPager {

	/**
	 * @var EntityId[]
	 */
	private $entityIds;

	/**
	 * @var int
	 */
	private $position = 0;

	public function __construct( array $entityIds ) {
		$this->entityIds = $entityIds;
	}

	/**
	 * @param int $limit
	 */
	public function fetchIds( $limit ) {
		$entityIds = array();

		while( !empty( $this->entityIds ) ) {
			for( $i = 0; $i < $limit; $i++ ) {
				if ( !empty( $this->entityIds ) ) {
					$entityIds[] = array_shift( $this->entityIds );
				}
			}
		}

		return $entityIds;
	}

}
