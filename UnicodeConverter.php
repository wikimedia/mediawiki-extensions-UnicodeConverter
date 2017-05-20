<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'UnicodeConverter' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['UnicodeConverter'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['UnicodeConverterAlias'] = __DIR__ . '/UnicodeConverter.alias.php';
	/* wfWarn(
		'Deprecated PHP entry point used for UnicodeConverter extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return;
} else {
	die( 'This version of the UnicodeConverter extension requires MediaWiki 1.25+' );
}
