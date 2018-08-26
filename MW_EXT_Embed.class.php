<?php

namespace MediaWiki\Extension\MetaStore;

require_once( __DIR__ . '/vendor/embed/src/autoloader.php' );

use OutputPage, Parser, Skin;
use Embed\Embed;

/**
 * Class MW_EXT_Embed
 */
class MW_EXT_Embed {

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws \MWException
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'embed', [ __CLASS__, 'onRenderTag' ] );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param string $url
	 *
	 * @return bool|string
	 */
	public static function onRenderTag( Parser $parser, $url = '' ) {
		// Argument: url.
		$getURL = MW_EXT_Kernel::outClear( $url ?? '' ?: '' );
		$outURL = $getURL;

		// Check URL.
		if ( empty( $outURL ) ) {
			$parser->addTrackingCategory( 'mw-ext-embed-error-category' );

			return null;
		}

		// Get URL data.
		$getData = Embed::create( $outURL );
		$outData = $getData->code;

		// Out HTML.
		$outHTML = '<div class="mw-ext-embed navigation-not-searchable"><div class="mw-ext-embed-body"><div class="mw-ext-embed-content">' . $outData . '</div></div></div>';

		// Out parser.
		$outParser = $parser->insertStripItem( $outHTML, $parser->mStripState );

		return $outParser;
	}

	/**
	 * Load resource function.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return bool
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( [ 'ext.mw.embed.styles' ] );

		return true;
	}
}
