<?php

$wgWBMappingConfigs = array(
	'terms' => array(
		'id' => array(
			'type' => 'string',
			'index' => 'not_analyzed'
		),
		'label' => array(
			'type' => 'string',
			'index' => 'not_analyzed'
		),
		'description' => array(
			'type' => 'string',
			'index' => 'not_analyzed'
		)
	)
);
