<?php

namespace Wikibase\Elastic\Index;

use Wikibase\Elastic\Connection;
use Wikibase\Elastic\Index\Builder\IndexBuilder;

class IndexLoader {

	public function get( $indexName ) {
		$index = $this->getIndex( $indexName );

		return $index;
	}

	private function getIndex( $indexName ) {
		$index = Connection::getIndex( $indexName );

		if ( !$index->exists() ) {
			$indexBuilder = new IndexBuilder();
			$index = $indexBuilder->buildIndex( $index );
		}

		return $index;
	}

}
