<?php

namespace Wikibase\Elastic\Index\Mapping;

class StatementMappingBuilder implements MappingBuilder {

	public function build() {
		$mapping = array();

		$typeName = 'statement';
		$properties = $this->getProperties();
		$mapping[$typeName] = $properties;

		return $mapping;
	}

	private function getProperties() {
		return array(
			'entity_id' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'property_id' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'data_type' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'snak_type' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'hash' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'value_string' => array(
				'type' => 'string',
				'index' => 'not_analyzed'
			),
			'value_geo' => array(
				'type' => 'geo_point'
			),
			'value_geo_attributes' => array(
				'properties' => array(
					'globe' => array(
						'type' => 'string',
						'index' => 'not_analyzed'
					),
					'precision' => array(
						'type' => 'float'
					)
				)
			),
		);
	}

}
