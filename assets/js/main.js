/**
 * Mammuts Theme - Main JavaScript
 *
 * @package Mammuts
 */

( function() {
    'use strict';

    /* ============================================
     * Header Scroll Effect
     * ============================================ */
    const header = document.getElementById( 'header-nav-bar' );
    const hasBanner = document.body.classList.contains( 'has-hero-banner' );

    function handleHeaderScroll() {
        if ( ! header ) return;
        // Use a small threshold so the transition feels responsive
        var threshold = hasBanner ? 30 : 50;
        if ( window.scrollY > threshold ) {
            header.classList.add( 'scrolled' );
        } else {
            header.classList.remove( 'scrolled' );
        }
    }

    window.addEventListener( 'scroll', handleHeaderScroll, { passive: true } );
    handleHeaderScroll();


    /* ============================================
     * Mobile Menu Toggle (Slide-in Drawer)
     * ============================================ */
    const menuToggle = document.getElementById( 'menu-toggle' );
    const mainNav    = document.getElementById( 'main-nav' );
    const navOverlay = document.getElementById( 'mobile-nav-overlay' );

    // Move nav and overlay to body so position:fixed works correctly
    // (avoids issues with sticky parent creating a new stacking context)
    function moveNavToBody() {
        if ( window.innerWidth <= 768 && mainNav && mainNav.parentElement !== document.body ) {
            document.body.appendChild( mainNav );
            if ( navOverlay ) {
                document.body.appendChild( navOverlay );
            }
        }
    }
    moveNavToBody();
    window.addEventListener( 'resize', moveNavToBody );

    function openMobileNav() {
        menuToggle.classList.add( 'active' );
        mainNav.classList.add( 'is-open' );
        if ( navOverlay ) navOverlay.classList.add( 'is-visible' );
        document.body.style.overflow = 'hidden';
        menuToggle.setAttribute( 'aria-expanded', 'true' );
    }

    function closeMobileNav() {
        menuToggle.classList.remove( 'active' );
        mainNav.classList.remove( 'is-open' );
        if ( navOverlay ) navOverlay.classList.remove( 'is-visible' );
        document.body.style.overflow = '';
        menuToggle.setAttribute( 'aria-expanded', 'false' );
    }

    if ( menuToggle && mainNav ) {
        menuToggle.addEventListener( 'click', function() {
            if ( mainNav.classList.contains( 'is-open' ) ) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        } );

        // Close menu on link click.
        // On mobile, submenus are always visible via CSS (no toggle).
        // Every link simply closes the drawer and navigates normally.
        mainNav.querySelectorAll( 'a' ).forEach( function( link ) {
            link.addEventListener( 'click', function() {
                var href = this.getAttribute( 'href' ) || '';
                // Skip #-placeholder links — nothing to navigate to.
                if ( href === '#' || href === '#/' || href === '' ) {
                    return;
                }
                closeMobileNav();
            } );
        } );

        // Close menu on overlay click
        if ( navOverlay ) {
            navOverlay.addEventListener( 'click', function() {
                closeMobileNav();
            } );
        }

        // Close on Escape key
        document.addEventListener( 'keydown', function( e ) {
            if ( e.key === 'Escape' && mainNav.classList.contains( 'is-open' ) ) {
                closeMobileNav();
            }
        } );
    }


    /* ============================================
     * Touch-friendly Dropdowns (Desktop / Tablet)
     *
     * Auf Touch-Geräten gibt es kein echtes :hover.
     * Beim ersten Tap wird das Dropdown geöffnet (und
     * alle anderen geschlossen). Beim zweiten Tap auf
     * denselben Link wird die Seite navigiert. Ein Tap
     * irgendwo anders schließt das Menü.
     *
     * Deckt sowohl Level-1-Dropdowns als auch Level-2
     * Sub-Parent Fly-outs ab.
     * ============================================ */
    ( function() {
        // Nur auf Screens > 768px (Desktop-Nav sichtbar)
        if ( window.matchMedia( '(max-width: 768px)' ).matches ) return;

        var isTouch = ( 'ontouchstart' in window ) || navigator.maxTouchPoints > 0;
        if ( ! isTouch ) return; // Desktop mit Maus → :hover reicht

        // ── Level 1: Top-Level Dropdowns ──
        var dropdowns = document.querySelectorAll( '.nav-item--has-dropdown' );
        var activeDropdown = null;

        dropdowns.forEach( function( item ) {
            var link = item.querySelector( ':scope > .nav-link' );
            if ( ! link ) return;

            link.addEventListener( 'click', function( e ) {
                if ( activeDropdown === item ) {
                    // Zweiter Tap → normal navigieren
                    return;
                }

                // Erster Tap → Dropdown öffnen, Navigation verhindern
                e.preventDefault();
                e.stopPropagation();

                // Alle Level-1-Dropdowns + Level-2-Fly-outs schließen
                dropdowns.forEach( function( d ) {
                    d.classList.remove( 'is-dropdown-open' );
                } );
                closeAllSubParents();

                item.classList.add( 'is-dropdown-open' );
                activeDropdown = item;
            } );
        } );

        // ── Level 2: Sub-Parent Fly-outs ──
        var subParents = document.querySelectorAll( '.nav-sub-parent' );
        var activeSubParent = null;

        function closeAllSubParents() {
            subParents.forEach( function( sp ) {
                sp.classList.remove( 'is-sub-open' );
            } );
            activeSubParent = null;
        }

        subParents.forEach( function( sp ) {
            var link = sp.querySelector( ':scope > .nav-dropdown-link' );
            if ( ! link ) return;

            link.addEventListener( 'click', function( e ) {
                e.stopPropagation(); // Verhindert, dass der Document-Handler alles schließt

                if ( activeSubParent === sp ) {
                    // Zweiter Tap → normal navigieren
                    return;
                }

                // Erster Tap → Fly-out öffnen, Navigation verhindern
                e.preventDefault();

                // Alle anderen Sub-Parents schließen
                closeAllSubParents();

                sp.classList.add( 'is-sub-open' );
                activeSubParent = sp;
            } );
        } );

        // Tap außerhalb schließt alles
        document.addEventListener( 'click', function() {
            dropdowns.forEach( function( d ) {
                d.classList.remove( 'is-dropdown-open' );
            } );
            activeDropdown = null;
            closeAllSubParents();
        } );
    } )();


    /* ============================================
     * Smooth Scroll for Anchor Links
     * ============================================ */
    document.querySelectorAll( 'a[href^="#"]' ).forEach( function( anchor ) {
        anchor.addEventListener( 'click', function( e ) {
            const targetId = this.getAttribute( 'href' );
            if ( targetId === '#' ) return;

            const target = document.querySelector( targetId );
            if ( target ) {
                e.preventDefault();
                const headerHeight = header ? header.offsetHeight : 0;
                const targetPos    = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo( {
                    top:      targetPos,
                    behavior: 'smooth'
                } );
            }
        } );
    } );


    /* ============================================
     * Scroll Animations (Intersection Observer)
     * ============================================ */
    const animateElements = document.querySelectorAll( '.animate-on-scroll' );

    if ( animateElements.length > 0 && 'IntersectionObserver' in window ) {
        const observer = new IntersectionObserver( function( entries ) {
            entries.forEach( function( entry ) {
                if ( entry.isIntersecting ) {
                    entry.target.classList.add( 'is-visible' );
                    observer.unobserve( entry.target );
                }
            } );
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        } );

        animateElements.forEach( function( el ) {
            observer.observe( el );
        } );
    }


    /* ============================================
     * Roster Position Filter
     * ============================================ */
    const filterBtns  = document.querySelectorAll( '.roster-filter' );
    const playerCards = document.querySelectorAll( '#roster-grid .player-card-wrapper' );

    if ( filterBtns.length > 0 && playerCards.length > 0 ) {
        filterBtns.forEach( function( btn ) {
            btn.addEventListener( 'click', function() {
                // Update active state
                filterBtns.forEach( function( b ) {
                    b.classList.remove( 'active' );
                } );
                this.classList.add( 'active' );

                const filter = this.getAttribute( 'data-filter' );

                playerCards.forEach( function( card ) {
                    if ( filter === 'all' ) {
                        card.style.display = '';
                        return;
                    }

                    const positions = card.getAttribute( 'data-positions' ) || '';
                    if ( positions.split( ',' ).indexOf( filter ) !== -1 ) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                } );
            } );
        } );
    }


    /* ============================================
     * Add animation classes to key sections
     * ============================================ */
    document.addEventListener( 'DOMContentLoaded', function() {
        const sections = document.querySelectorAll(
            '.match-card, .player-card, .news-card, .section-header'
        );

        sections.forEach( function( el, index ) {
            el.classList.add( 'animate-on-scroll' );
            el.style.transitionDelay = ( index % 6 ) * 0.08 + 's';
        } );

        // Re-observe newly tagged elements
        if ( 'IntersectionObserver' in window ) {
            const lateObserver = new IntersectionObserver( function( entries ) {
                entries.forEach( function( entry ) {
                    if ( entry.isIntersecting ) {
                        entry.target.classList.add( 'is-visible' );
                        lateObserver.unobserve( entry.target );
                    }
                } );
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            } );

            sections.forEach( function( el ) {
                lateObserver.observe( el );
            } );
        }
    } );

} )();


/* ============================================
 * League Table Tooltips
 * Adds full names to abbreviated column headers
 * ============================================ */
( function() {
    'use strict';

    var abbreviations = {
        'pos':     'Position',
        'p':       'Played',
        'gp':      'Games Played',
        'w':       'Wins',
        'l':       'Losses',
        't':       'Ties',
        'd':       'Draws',
        'otl':     'Overtime Losses',
        'pct':     'Win Percentage',
        'f':       'Points For',
        'pf':      'Points For',
        'a':       'Points Against',
        'pa':      'Points Against',
        'gf':      'Goals For',
        'ga':      'Goals Against',
        'gd':      'Goal Difference',
        'pts':     'Points',
        'net pts': 'Net Points',
        'td':      'Touchdowns',
        'streak':  'Current Streak',
        'last5':   'Last 5 Games',
        'last 5':  'Last 5 Games',
        'home':    'Home Record',
        'away':    'Away Record',
        'rs':      'Runs Scored',
        'ra':      'Runs Against',
        'rw':      'Road Wins',
        'rl':      'Road Losses'
    };

    document.addEventListener( 'DOMContentLoaded', function() {
        var headers = document.querySelectorAll( '.sp-league-table thead th' );

        headers.forEach( function( th ) {
            var text = ( th.textContent || th.innerText ).trim().toLowerCase();
            if ( abbreviations[ text ] ) {
                th.setAttribute( 'title', abbreviations[ text ] );

                // Create custom tooltip element
                var tooltip = document.createElement( 'span' );
                tooltip.className = 'sp-table-tooltip';
                tooltip.textContent = abbreviations[ text ];
                th.style.position = 'relative';
                th.appendChild( tooltip );
            }
        } );
    } );
} )();

/**
 * Ligatabelle Mobile: data-th Labels auf <td> setzen.
 *
 * SportsPress setzt data-th manchmal nicht. Wir lesen die Header-Texte
 * aus dem <thead> und schreiben sie als data-th auf die entsprechenden
 * <td>-Spalten — das ist die Grundlage für die CSS ::before Labels.
 */
( function() {
    function mammutsTableLabels() {
        document.querySelectorAll( '.sp-league-table' ).forEach( function( table ) {
            // Header-Texte einsammeln (Index → Label)
            var headers = [];
            table.querySelectorAll( 'thead th' ).forEach( function( th ) {
                // Nur direkte Text-Knoten lesen, NICHT den Tooltip-Span
                var label = '';
                th.childNodes.forEach( function( node ) {
                    if ( node.nodeType === 3 ) { // TEXT_NODE
                        label += node.textContent;
                    }
                } );
                headers.push( label.trim() );
            } );

            if ( headers.length === 0 ) return;

            // data-th auf jede td setzen (falls noch nicht vorhanden)
            table.querySelectorAll( 'tbody tr' ).forEach( function( row ) {
                var cells = row.querySelectorAll( 'td' );
                cells.forEach( function( td, i ) {
                    if ( ! td.getAttribute( 'data-th' ) && headers[ i ] ) {
                        td.setAttribute( 'data-th', headers[ i ] );
                    }
                } );
            } );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', mammutsTableLabels );
    } else {
        mammutsTableLabels();
    }
} )();


/* ============================================
 * Profile Popup Modal
 * Opens an overlay popup when clicking on
 * player cards or staff cards that have a
 * description (data-popup-content).
 * ============================================ */
( function() {
    'use strict';

    var modal       = null;
    var modalInner  = null;
    var lastFocused = null;
    var scrollY_saved = 0;

    /**
     * Create the modal DOM structure (once, lazily).
     */
    function ensureModal() {
        if ( modal ) return;

        modal = document.createElement( 'div' );
        modal.className = 'mammuts-popup-overlay';
        modal.setAttribute( 'role', 'dialog' );
        modal.setAttribute( 'aria-modal', 'true' );
        modal.setAttribute( 'aria-label', 'Profil-Details' );

        modal.innerHTML =
            '<div class="mammuts-popup-backdrop"></div>' +
            '<div class="mammuts-popup-scroll">' +
                '<div class="mammuts-popup-card" role="document">' +
                    '<button class="mammuts-popup-close" aria-label="Schließen">' +
                        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                    '</button>' +
                    '<div class="mammuts-popup-body"></div>' +
                '</div>' +
            '</div>';

        document.body.appendChild( modal );

        modalInner = modal.querySelector( '.mammuts-popup-body' );

        // Close on backdrop click
        modal.querySelector( '.mammuts-popup-backdrop' ).addEventListener( 'click', closeModal );
        modal.querySelector( '.mammuts-popup-scroll' ).addEventListener( 'click', function( e ) {
            // Close when clicking in the scroll area but outside the card
            if ( e.target === this ) closeModal();
        } );

        // Close button
        modal.querySelector( '.mammuts-popup-close' ).addEventListener( 'click', closeModal );
    }

    /**
     * Build HTML for a player popup.
     */
    function buildPlayerHTML( data ) {
        var html = '<div class="mammuts-popup-layout mammuts-popup-layout--player">';

        // Photo column
        if ( data.image ) {
            html += '<div class="mammuts-popup-photo">';
            html += '<img src="' + data.image + '" alt="' + data.name + '" loading="lazy">';
            if ( data.number ) {
                html += '<span class="mammuts-popup-number">#' + data.number + '</span>';
            }
            html += '</div>';
        }

        // Info column
        html += '<div class="mammuts-popup-info">';

        if ( data.position ) {
            html += '<span class="mammuts-popup-badge">' + data.position + '</span>';
        }

        html += '<h2 class="mammuts-popup-name">';
        if ( data.number && ! data.image ) {
            html += '<span class="mammuts-popup-inline-nr">#' + data.number + '</span> ';
        }
        html += data.name + '</h2>';

        if ( data.team ) {
            html += '<p class="mammuts-popup-team">' + data.team + '</p>';
        }

        // Stats row
        var stats = [];
        if ( data.number ) stats.push( { val: data.number, lbl: 'Nummer' } );
        if ( data.height ) stats.push( { val: data.height + ' cm', lbl: 'Größe' } );
        if ( data.weight ) stats.push( { val: data.weight + ' kg', lbl: 'Gewicht' } );

        if ( stats.length ) {
            html += '<div class="mammuts-popup-stats">';
            for ( var i = 0; i < stats.length; i++ ) {
                html += '<div class="mammuts-popup-stat">';
                html += '<span class="mammuts-popup-stat-val">' + stats[i].val + '</span>';
                html += '<span class="mammuts-popup-stat-lbl">' + stats[i].lbl + '</span>';
                html += '</div>';
            }
            html += '</div>';
        }

        // Description
        if ( data.content ) {
            html += '<div class="mammuts-popup-desc">' + data.content + '</div>';
        }

        html += '</div>'; // .mammuts-popup-info
        html += '</div>'; // .mammuts-popup-layout

        return html;
    }

    /**
     * Build HTML for a staff popup.
     */
    function buildStaffHTML( data ) {
        var html = '<div class="mammuts-popup-layout mammuts-popup-layout--staff">';

        // Photo column
        if ( data.image ) {
            html += '<div class="mammuts-popup-photo">';
            html += '<img src="' + data.image + '" alt="' + data.name + '" loading="lazy">';
            html += '</div>';
        }

        // Info column
        html += '<div class="mammuts-popup-info">';

        if ( data.role ) {
            html += '<span class="mammuts-popup-badge">' + data.role + '</span>';
        }

        html += '<h2 class="mammuts-popup-name">' + data.name + '</h2>';

        // Description
        if ( data.content ) {
            html += '<div class="mammuts-popup-desc">' + data.content + '</div>';
        }

        html += '</div>';
        html += '</div>';

        return html;
    }

    /**
     * Open the modal for a given card element.
     */
    function openModal( card ) {
        ensureModal();

        var type    = card.getAttribute( 'data-popup-type' );
        var content = card.getAttribute( 'data-popup-content' ) || '';

        if ( ! content ) return;

        var data = {
            name:     card.getAttribute( 'data-popup-name' ) || '',
            image:    card.getAttribute( 'data-popup-image' ) || '',
            content:  content
        };

        if ( type === 'player' ) {
            data.number   = card.getAttribute( 'data-popup-number' ) || '';
            data.position = card.getAttribute( 'data-popup-position' ) || '';
            data.team     = card.getAttribute( 'data-popup-team' ) || '';
            data.height   = card.getAttribute( 'data-popup-height' ) || '';
            data.weight   = card.getAttribute( 'data-popup-weight' ) || '';
            data.link     = card.getAttribute( 'data-popup-link' ) || '';
            modalInner.innerHTML = buildPlayerHTML( data );
        } else {
            data.role = card.getAttribute( 'data-popup-role' ) || '';
            modalInner.innerHTML = buildStaffHTML( data );
        }

        // Set aria label
        modal.setAttribute( 'aria-label', data.name );

        // Remember focus origin
        lastFocused = document.activeElement;

        // Lock body scroll (iOS-safe: position:fixed preserves scroll pos)
        scrollY_saved = window.scrollY;
        document.body.classList.add( 'mammuts-popup-body-lock' );
        document.body.style.top = '-' + scrollY_saved + 'px';

        // Show
        modal.classList.add( 'is-open' );

        // Focus the close button
        var closeBtn = modal.querySelector( '.mammuts-popup-close' );
        if ( closeBtn ) {
            setTimeout( function() { closeBtn.focus(); }, 80 );
        }
    }

    /**
     * Close the modal.
     */
    function closeModal() {
        if ( ! modal ) return;

        modal.classList.remove( 'is-open' );

        // Unlock body scroll and restore position
        document.body.classList.remove( 'mammuts-popup-body-lock' );
        document.body.style.top = '';
        window.scrollTo( 0, scrollY_saved || 0 );

        // Restore focus
        if ( lastFocused ) {
            lastFocused.focus();
            lastFocused = null;
        }
    }

    /**
     * Bind click / keyboard handlers via event delegation.
     */
    function initPopups() {
        document.addEventListener( 'click', function( e ) {
            var card = e.target.closest( '[data-popup-content]' );
            if ( card ) {
                e.preventDefault();
                openModal( card );
            }
        } );

        document.addEventListener( 'keydown', function( e ) {
            // Open on Enter / Space when a popup card is focused
            if ( ( e.key === 'Enter' || e.key === ' ' ) && e.target.closest( '[data-popup-content]' ) ) {
                e.preventDefault();
                openModal( e.target.closest( '[data-popup-content]' ) );
                return;
            }

            // Close on Escape
            if ( e.key === 'Escape' && modal && modal.classList.contains( 'is-open' ) ) {
                closeModal();
            }
        } );
    }

    // Init
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initPopups );
    } else {
        initPopups();
    }
} )();

/* ============================================
 * Sibling Nav – Sticky shadow via IntersectionObserver
 *
 * A sentinel <div> is injected right before .sibling-nav.
 * When it scrolls out of view (= the nav is stuck), the
 * 'is-stuck' class is added to show a subtle shadow.
 * ============================================ */
( function() {
    'use strict';

    var nav = document.querySelector( '.sibling-nav' );
    if ( ! nav ) return;

    // ── Sticky shadow sentinel ──
    var sentinel = document.createElement( 'div' );
    sentinel.setAttribute( 'aria-hidden', 'true' );
    sentinel.style.height = '0';
    sentinel.style.width = '100%';
    sentinel.style.pointerEvents = 'none';
    nav.parentNode.insertBefore( sentinel, nav );

    if ( 'IntersectionObserver' in window ) {
        var observer = new IntersectionObserver( function( entries ) {
            nav.classList.toggle( 'is-stuck', ! entries[0].isIntersecting );
        }, {
            rootMargin: '-' + getComputedStyle( document.documentElement )
                .getPropertyValue( '--header-height' ).trim() + ' 0px 0px 0px',
            threshold: 0
        } );
        observer.observe( sentinel );
    }

    // ── Scroll indicators ──
    var wraps = document.querySelectorAll( '.sibling-nav-scroll-wrap' );
    wraps.forEach( function( wrap ) {
        var scroller = wrap.querySelector( '.sibling-nav-scroll' );
        if ( ! scroller ) return;

        var threshold = 4; // px tolerance for edge detection

        function updateIndicators() {
            var sl  = scroller.scrollLeft;
            var max = scroller.scrollWidth - scroller.clientWidth;

            wrap.classList.toggle( 'can-scroll-left', sl > threshold );
            wrap.classList.toggle( 'can-scroll-right', sl < max - threshold );
        }

        scroller.addEventListener( 'scroll', updateIndicators, { passive: true } );
        window.addEventListener( 'resize', updateIndicators, { passive: true } );

        // Initial check (after fonts load / layout settles)
        if ( document.fonts && document.fonts.ready ) {
            document.fonts.ready.then( updateIndicators );
        }
        // Also run on next frame as fallback
        requestAnimationFrame( function() {
            requestAnimationFrame( updateIndicators );
        } );
    } );
} )();

/* ============================================
 * News – Scroll to card & highlight on return
 *
 * When navigating back from a single news post,
 * the URL contains #post-{ID}. This scrolls to
 * that card and briefly highlights it.
 * ============================================ */
( function() {
    'use strict';

    function highlightCard() {
        var hash = window.location.hash;
        if ( ! hash || hash.indexOf( '#post-' ) !== 0 ) return;

        var card = document.querySelector( hash );
        if ( ! card ) return;

        // Scroll into view (smooth, respects scroll-margin-top)
        card.scrollIntoView( { behavior: 'smooth', block: 'center' } );

        // Highlight animation
        card.classList.add( 'is-highlight' );
        card.addEventListener( 'animationend', function() {
            card.classList.remove( 'is-highlight' );
        }, { once: true } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', highlightCard );
    } else {
        // Small delay to ensure layout is settled
        requestAnimationFrame( function() {
            requestAnimationFrame( highlightCard );
        } );
    }
} )();

/* ============================================
 * Equalise Player-Card Heights per Row
 * ============================================ */
( function() {
    'use strict';

    function equaliseCardRows() {
        var cards = document.querySelectorAll( '.roster-grid .player-card' );
        if ( ! cards.length ) return;

        // 1. Reset heights so we can measure natural sizes
        cards.forEach( function( c ) { c.style.minHeight = ''; } );

        // 2. Group cards by their top offset (= same visual row)
        var rows = {};
        cards.forEach( function( c ) {
            var top = Math.round( c.getBoundingClientRect().top );
            if ( ! rows[ top ] ) rows[ top ] = [];
            rows[ top ].push( c );
        } );

        // 3. For each row, find the tallest card and apply that height to all
        Object.keys( rows ).forEach( function( key ) {
            var row = rows[ key ];
            var maxH = 0;
            row.forEach( function( c ) {
                var h = c.getBoundingClientRect().height;
                if ( h > maxH ) maxH = h;
            } );
            row.forEach( function( c ) {
                c.style.minHeight = maxH + 'px';
            } );
        } );
    }

    // Run after layout is ready + images loaded
    window.addEventListener( 'load', equaliseCardRows );
    window.addEventListener( 'resize', equaliseCardRows );
} )();
