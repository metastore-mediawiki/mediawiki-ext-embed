<?php

namespace MediaWiki\Extension\MW_EXT_Embed;

require_once( __DIR__ . '/vendor/embed/src/autoloader.php' );

use Embed\Embed;
use OutputPage, Parser, Skin;

/**
 * Class MW_EXT_Embed
 * ------------------------------------------------------------------------------------------------------------------ */
class MW_EXT_Embed {

	/**
	 * Clear DATA (escape html).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function clearData( $string ) {
		$outString = htmlspecialchars( trim( $string ), ENT_QUOTES );

		return $outString;
	}

	/**
	 * * Clear URL.
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function clearURL( $string ) {
		$outString = rawurlencode( trim( $string ) );

		return $outString;
	}

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'embed', __CLASS__ . '::onRenderTag' );

		//$parser->setFunctionHook( 'embed-steam', __CLASS__ . '::onRenderSteam' );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param string $url
	 *
	 * @return bool|string
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderTag( Parser $parser, $url = '' ) {
		// Argument: URL.
		$getURL = self::clearData( $url ?? '' ?: '' );
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
		$outHTML = '<div class="mw-ext-embed"><div class="mw-ext-embed-body"><div class="mw-ext-embed-content">' . $outData . '</div></div></div>';

		// Out parser.
		$outParser = $parser->insertStripItem( $outHTML, $parser->mStripState );

		return $outParser;
	}

	/**
	 * Render `steam` function.
	 *
	 * @param Parser $parser
	 * @param string $url
	 *
	 * @return bool|string
	 * -------------------------------------------------------------------------------------------------------------- */
	/*
	public static function onRenderSteam( Parser $parser, $url = '' ) {
		$getURL = self::clearData( $url ?? '' ?: '' );

		if ( empty( $getURL ) ) {
			$parser->addTrackingCategory( 'mw-ext-embed-error-category' );

			return false;
		}

		$getData = Embed::create( $getURL );
		$outCode = $getData->image;

		$output = '<div class="mw-ext-embed"><div class="mw-ext-embed-container">' . $outCode . '</div></div>';

		return $parser->insertStripItem( $output, $parser->mStripState );
	}
	*/

	/**
	 * Load resource function.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( [ 'ext.mw.embed.styles' ] );
		$out->addModules( [ 'ext.mw.embed' ] );

		return true;
	}
}
