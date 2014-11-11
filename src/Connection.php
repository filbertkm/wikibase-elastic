<?php

namespace Wikibase\Elastic;

use ElasticaConnection;

class Connection extends ElasticaConnection {

	public function __construct( array $serverList = null ) {
		$this->serverList = $serverList ? $serverList : $GLOBALS['wgCirrusSearchServers'];
	}

	public function getServerList() {
		return $this->serverList;
	}

}
