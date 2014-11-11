<?php
/**
 * Initialization for WikibaseElastic extension
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not a valid MediaWiki entry point.' );
}

if ( !defined( 'WBL_VERSION' ) ) {
	die( 'WikibaseElastic requires WikibaseLib extension.' );
}

/* Setup */
require __DIR__ . '/vendor/autoload.php';

call_user_func( function() {
	global $wgExtensionCredits, $wgExtensionMessagesFiles;

	$app = new \Wikibase\Elastic\App();

	$wgExtensionCredits['wikibase'][] = array(
		'path' => __FILE__,
		'name' => 'WikibaseElastic',
		'author' => array( 'Katie Filbert' ),
		'version'  => '0.1',
		'url' => 'https://www.mediawiki.org/wiki/Extension:WikibaseElastic',
		'descriptionmsg' => 'wikibaseelastic-desc',
	);

	$wgExtensionMessagesFiles['WikibaseElastic'] = __DIR__ . '/WikibaseElastic.i18n.php';
} );
