/**
 * Mammuts Theme — Admin Sortable (AJAX)
 * v3 — uses stop + deferred save, no containment
 */
( function( $ ) {
    'use strict';

    var data = window.mammuts_sortable_data || {};

    function showMsg( $el, msg, color ) {
        var $m = $el.next( '.mammuts-sort-msg' );
        if ( ! $m.length ) {
            $m = $( '<p class="mammuts-sort-msg"></p>' ).insertAfter( $el );
        }
        $m.stop( true ).show().css( { fontSize: '13px', margin: '10px 0', color: color, fontWeight: 600 } ).text( msg );
        if ( color === '#00a32a' ) {
            setTimeout( function() { $m.fadeOut(); }, 3000 );
        }
    }

    function saveOrder( $container, ajaxAction, newValue, label ) {
        showMsg( $container, '⏳ Speichere ' + label + '...', '#dba617' );

        $.ajax( {
            url:    data.ajax_url,
            method: 'POST',
            data: {
                action:  ajaxAction,
                post_id: data.post_id,
                order:   newValue,
                _nonce:  data.nonce
            }
        } )
        .done( function( resp ) {
            if ( resp && resp.success ) {
                showMsg( $container, '✓ ' + label + ' gespeichert! (Seite neu laden zum Prüfen)', '#00a32a' );
            } else {
                var err = ( resp && resp.data ) ? resp.data : 'Unbekannter Fehler';
                showMsg( $container, '✗ Fehler: ' + err, '#d63638' );
            }
        } )
        .fail( function( xhr ) {
            showMsg( $container, '✗ AJAX fehlgeschlagen: ' + xhr.status + ' ' + xhr.statusText, '#d63638' );
        } );
    }

    // ── Staff Order ──
    function initStaffOrder() {
        var $s = $( '#mammuts-staff-sortable' );
        if ( ! $s.length || $s.data( 'mammuts-init' ) ) return;
        $s.data( 'mammuts-init', true );

        if ( typeof $.fn.sortable === 'undefined' ) {
            showMsg( $s, '⚠ jQuery UI Sortable fehlt — bitte Seite neu laden.', '#d63638' );
            return;
        }

        showMsg( $s, 'ℹ Sortierbar initialisiert. Post-ID: ' + data.post_id, '#666' );

        $s.sortable( {
            axis:      'y',
            items:     '> .mammuts-staff-sort-item',
            tolerance: 'pointer',
            cursor:    'grabbing',
            opacity:   0.8,
            placeholder: 'mammuts-sort-placeholder',
            stop: function() {
                // Read new order after drop
                var ids = [];
                $s.children( '.mammuts-staff-sort-item' ).each( function( i ) {
                    ids.push( $( this ).attr( 'data-id' ) );
                    $( this ).find( '.mammuts-staff-sort-num' ).text( i + 1 );
                } );
                var val = ids.join( ',' );
                $( '#mammuts_staff_order' ).val( val );
                saveOrder( $s, 'mammuts_save_staff_order', val, 'Staff-Reihenfolge' );
            }
        } );
    }

    // ── Role Priority ──
    function initRolePriority() {
        var $s = $( '#mammuts-role-sortable' );
        if ( ! $s.length || $s.data( 'mammuts-init' ) ) return;
        $s.data( 'mammuts-init', true );

        if ( typeof $.fn.sortable === 'undefined' ) {
            showMsg( $s, '⚠ jQuery UI Sortable fehlt.', '#d63638' );
            return;
        }

        $s.sortable( {
            axis:      'y',
            items:     '> .mammuts-role-sort-item',
            tolerance: 'pointer',
            cursor:    'grabbing',
            opacity:   0.8,
            placeholder: 'mammuts-sort-placeholder mammuts-sort-placeholder--sm',
            stop: function() {
                var slugs = [];
                $s.children( '.mammuts-role-sort-item' ).each( function( i ) {
                    slugs.push( $( this ).attr( 'data-slug' ) );
                    $( this ).find( 'span:last' ).text( '#' + ( i + 1 ) );
                } );
                var val = slugs.join( ',' );
                $( '#mammuts_role_priority' ).val( val );
                saveOrder( $s, 'mammuts_save_role_priority', val, 'Rollen-Reihenfolge' );
            }
        } );
    }

    // ── Init: multiple strategies ──
    function tryInit() {
        initStaffOrder();
        initRolePriority();
    }

    // 1. DOM ready
    $( tryInit );

    // 2. Gutenberg ready
    if ( window.wp && wp.domReady ) {
        wp.domReady( function() {
            setTimeout( tryInit, 800 );
        } );
    }

    // 3. Periodic retry for 8 seconds
    var attempts = 0;
    var retryTimer = setInterval( function() {
        attempts++;
        tryInit();
        if ( attempts > 16 ) clearInterval( retryTimer );
    }, 500 );

} )( jQuery );
