<?php

namespace Wikibase\Elastic\Lookup;

use Elastica\Filter\GeoDistance;
use Elastica\Filter\Term;
use Elastica\Filter\Type;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\MatchAll;
use Elastica\ResultSet;
use Elastica\Search;

class GeoQuery {

	/**
	 * @var Index
	 */
	private $index;

	private $limit = 10;

	public function __construct( Index $index ) {
		$this->index = $index;
	}

	/**
	 * @return string
	 */
	public function getNearby( $latitude, $longitude ) {
		$query = new Query( new MatchAll() );

		$filter = new GeoDistance(
			'value_geo',
			array(
				'lat' => $latitude,
				'lon' => $longitude
			),
			'1000km'
		);

		$query->setFilter( $filter );

		$search = new Search( $this->index->getClient() );
		$search->addType( new \Elastica\Type( $this->index, 'statement' ) );
		$search->addIndex( $this->index );
		$search->setQuery( $query );

		return $search->search();
	}

}
