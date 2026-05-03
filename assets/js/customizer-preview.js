/**
 * Mammuts Theme — Customizer Live Preview
 *
 * Updates CSS custom properties in real time so the user
 * can see changes without reloading the preview pane.
 *
 * Covers:
 *  - Card background colour + opacity
 *  - Card background gradient (type, angle, two colours + opacities + stops)
 *  - Card hover background colour
 *  - Card text colours (title, excerpt, category, date, link)
 *  - Display font & body font (Google Fonts loaded dynamically)
 */
( function( $ ) {
    'use strict';

    // ── Helpers ──────────────────────────────────────────────────

    function setProp( prop, value ) {
        document.documentElement.style.setProperty( prop, value );
    }

    function hexToRgba( hex, opacity ) {
        hex = hex.replace( '#', '' );
        var r = parseInt( hex.substring( 0, 2 ), 16 );
        var g = parseInt( hex.substring( 2, 4 ), 16 );
        var b = parseInt( hex.substring( 4, 6 ), 16 );
        var a = Math.round( opacity ) / 100;
        return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
    }

    // ── Gradient state ────────────────────────────────────────────

    var grad = {
        enabled  : !! wp.customize( 'mammuts_card_gradient_enabled' )(),
        type     : wp.customize( 'mammuts_card_gradient_type' )(),
        angle    : parseInt( wp.customize( 'mammuts_card_gradient_angle' )(), 10 ),
        color1   : wp.customize( 'mammuts_card_gradient_color1' )(),
        opacity1 : parseInt( wp.customize( 'mammuts_card_gradient_opacity1' )(), 10 ),
        stop1    : parseInt( wp.customize( 'mammuts_card_gradient_stop1' )(), 10 ),
        color2   : wp.customize( 'mammuts_card_gradient_color2' )(),
        opacity2 : parseInt( wp.customize( 'mammuts_card_gradient_opacity2' )(), 10 ),
        stop2    : parseInt( wp.customize( 'mammuts_card_gradient_stop2' )(), 10 ),
    };

    // Flat background state (used when gradient is off)
    var flat = {
        color   : wp.customize( 'mammuts_card_bg_color' )(),
        opacity : parseInt( wp.customize( 'mammuts_card_bg_opacity' )(), 10 ),
    };

    function buildGradient() {
        var rgba1 = hexToRgba( grad.color1, grad.opacity1 );
        var rgba2 = hexToRgba( grad.color2, grad.opacity2 );
        if ( grad.type === 'radial' ) {
            return 'radial-gradient(ellipse at center, ' + rgba1 + ' ' + grad.stop1 + '%, ' + rgba2 + ' ' + grad.stop2 + '%)';
        }
        return 'linear-gradient(' + grad.angle + 'deg, ' + rgba1 + ' ' + grad.stop1 + '%, ' + rgba2 + ' ' + grad.stop2 + '%)';
    }

    function updateCardBg() {
        if ( grad.enabled ) {
            setProp( '--color-bg-card', buildGradient() );
        } else {
            setProp( '--color-bg-card', hexToRgba( flat.color, flat.opacity ) );
        }
    }

    // ── Flat background bindings ──────────────────────────────────

    wp.customize( 'mammuts_card_bg_color', function( value ) {
        value.bind( function( v ) { flat.color = v; updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_bg_opacity', function( value ) {
        value.bind( function( v ) { flat.opacity = parseInt( v, 10 ); updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_bg_hover_color', function( value ) {
        value.bind( function( v ) { setProp( '--color-bg-card-hover', v ); } );
    } );

    // ── Gradient bindings ─────────────────────────────────────────

    wp.customize( 'mammuts_card_gradient_enabled', function( value ) {
        value.bind( function( v ) { grad.enabled = !! v; updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_type', function( value ) {
        value.bind( function( v ) { grad.type = v; updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_angle', function( value ) {
        value.bind( function( v ) { grad.angle = parseInt( v, 10 ); updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_color1', function( value ) {
        value.bind( function( v ) { grad.color1 = v; updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_opacity1', function( value ) {
        value.bind( function( v ) { grad.opacity1 = parseInt( v, 10 ); updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_stop1', function( value ) {
        value.bind( function( v ) { grad.stop1 = parseInt( v, 10 ); updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_color2', function( value ) {
        value.bind( function( v ) { grad.color2 = v; updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_opacity2', function( value ) {
        value.bind( function( v ) { grad.opacity2 = parseInt( v, 10 ); updateCardBg(); } );
    } );
    wp.customize( 'mammuts_card_gradient_stop2', function( value ) {
        value.bind( function( v ) { grad.stop2 = parseInt( v, 10 ); updateCardBg(); } );
    } );

    // ── Card text colours ─────────────────────────────────────────

    var cardTextMap = {
        mammuts_card_title_color:    '--color-card-title',
        mammuts_card_excerpt_color:  '--color-card-excerpt',
        mammuts_card_category_color: '--color-card-category',
        mammuts_card_date_color:     '--color-card-date',
        mammuts_card_link_color:     '--color-card-link'
    };

    $.each( cardTextMap, function( setting, prop ) {
        wp.customize( setting, function( value ) {
            value.bind( function( v ) { setProp( prop, v ); } );
        } );
    } );

    // ── Typography ────────────────────────────────────────────────

    var injectedFonts = {};

    function ensureFontLoaded( fontName ) {
        if ( ! fontName || injectedFonts[ fontName ] ) {
            return;
        }
        var link  = document.createElement( 'link' );
        link.rel  = 'stylesheet';
        link.href = 'https://fonts.googleapis.com/css2?family='
                  + encodeURIComponent( fontName )
                  + ':wght@300;400;500;600;700;800;900&display=swap';
        document.head.appendChild( link );
        injectedFonts[ fontName ] = true;
    }

    wp.customize( 'mammuts_font_display', function( value ) {
        value.bind( function( v ) {
            ensureFontLoaded( v );
            setProp( '--font-display', "'" + v + "', 'Impact', sans-serif" );
        } );
    } );

    wp.customize( 'mammuts_font_body', function( value ) {
        value.bind( function( v ) {
            ensureFontLoaded( v );
            setProp( '--font-body', "'" + v + "', 'Helvetica Neue', sans-serif" );
        } );
    } );

} )( jQuery );
