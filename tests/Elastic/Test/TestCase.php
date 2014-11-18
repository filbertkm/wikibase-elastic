<?php

namespace Wikibase\Elastic\Test;

use Elastica\Client;
use Elastica\Index;

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @param string $name

     * @return Index
     */
    protected function getIndex( $name ) {
        $client = new Client();

		return $client->getIndex( $name );
	}

	/**
	 * @param string $name
	 *
	 * @return Index
	 */
	protected function newIndex( $name ) {
		$index = $this->getIndex( $name );
		$index->create(
			array(
				'index' => array(
					'number_of_shards' => 1,
					'number_of_replicas' => 0
				)
			),
			true
		);

        return $index;
    }
}
