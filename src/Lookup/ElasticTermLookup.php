<?php

namespace Wikibase\Elastic\Lookup;

use Elastica\Filter\Term;
use Elastica\Filter\Type;
use Elastica\Index;
use Elastica\Query;
use Elastica\ResultSet;
use OutOfBoundsException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\Lib\Store\TermLookup;
use Wikibase\Utils;

class ElasticTermLookup implements TermLookup {

	/**
	 * @var Index
	 */
	private $index;

	/**
	 * @var string[]
	 */
	private $languageCodes;

	public function __construct( Index $index ) {
		$this->index = $index;
		$this->languageCodes = Utils::getLanguageCodes();
	}

	public function getLabel( EntityId $entityId, $languageCode ) {
		$results = $this->queryTerms( $entityId, array( $languageCode ) );

		return $this->extractLabelFromResults( $results );
	}

	public function getLabels( EntityId $entityId ) {
		return $this->getTerms( $entityId, 'label' );
	}

	public function getDescription( EntityId $entityId, $languageCode ) {
		$results = $this->queryTerms( $entityId, $languageCode );

		return $this->extractDescriptionFromResults( $results );
	}

	public function getDescriptions( EntityId $entityId ) {
		return $this->getTerms( $entityId, 'description' );
	}

	private function getTerms( EntityId $entityId, $termType ) {
		$chunks = array_chunk( $this->languageCodes, 12 );

		$terms = array();

		foreach( $chunks as $chunk ) {
			$result = $this->queryTerms( $entityId, $chunk );
			$terms += $this->extractMatchesFromResults( $result, $termType );
		}

		return $terms;
	}

	private function queryTerms( EntityId $entityId, array $languageCodes ) {
		$query = new Query();
		$query->setFilter( $this->getFilterForId( $entityId ) );

		$search = new \Elastica\Search( $this->index->getClient() );

		foreach( $languageCodes as $languageCode ) {
			$search->addType( new \Elastica\Type( $this->index, 'terms_' . $languageCode ) );
		}

		$search->setQuery( $query );
		wfDebugLog( 'wikidata', $entityId->getSerialization() );

		return $search->search();
	}

	private function getFilterForId( EntityId $entityId ) {
		$termFilter = new Term();
		$termFilter->setTerm( 'id', $entityId->getSerialization() );

		return $termFilter;
	}

	private function extractLabelFromResults( ResultSet $results ) {
		return $this->extractFieldFromResults( $results, 'label' );
	}

	private function extractDescriptionFromResults( ResultSet $results ) {
		return $this->extractFieldFromResults( $results, 'description' );
	}

	private function extractFieldFromResults( ResultSet $results, $field ) {
		foreach( $results as $result ) {
			$source = $result->getSource();

			if ( isset( $source[$field] ) ) {
				return $source[$field];
			}
		}

		throw new OutOfBoundsException( 'Label not found' );
	}

	private function extractMatchesFromResults( ResultSet $results, $field ) {
		$matches = array();

		foreach( $results as $result ) {
			$source = $result->getSource();

			if ( isset( $source[$field] ) ) {
				$languageCode = substr( $result->getType(), 6 );
				$matches[$languageCode] = $source[$field];
			}
		}

		return $matches;
	}

}
