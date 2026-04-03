<?php

/**
 * BODHIMURASU — SHOP PAGE v6
 * Shortcode: [bodhi_shop]
 *
 * HOW TO SET AUTHOR NAME:
 *   WooCommerce → Edit Product → "Product short description"
 *   Type ONLY the author name — e.g.  ஓ.ரா.ந.கிருஷ்ணன்
 *   The filter reads this field; the name is hidden on the product card.
 */

// ── 1. FULL-WIDTH ──────────────────────────────────────────────────────────────
function bodhi_shop_fullwidth_v6() {
    echo '<style>
        .woocommerce-breadcrumb,.woocommerce-result-count,
        .woocommerce-ordering,ul.products,.wc-block-grid{ display:none !important; }
        #secondary,.widget-area,aside.sidebar{ display:none !important; }
        .entry-content .bshop6,.post-content .bshop6,.page-content .bshop6{
            width:100% !important; max-width:100% !important;
            margin-left:0 !important; margin-right:0 !important;
            padding-left:0 !important; padding-right:0 !important;
        }
    </style>';
}
add_action( 'wp_head', 'bodhi_shop_fullwidth_v6', 99 );


// ── 2. AJAX ADD-TO-CART ────────────────────────────────────────────────────────
function bodhi_ajax_add_to_cart_v6() {
    check_ajax_referer( 'bodhi_atc_nonce_v6', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error( array( 'message' => 'Invalid product' ) );
    $key = WC()->cart->add_to_cart( $product_id, 1 );
    if ( $key ) {
        WC()->cart->calculate_totals();
        wp_send_json_success( array(
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_url'   => wc_get_cart_url(),
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'Could not add to cart' ) );
    }
}
add_action( 'wp_ajax_bodhi_add_to_cart_v6',        'bodhi_ajax_add_to_cart_v6' );
add_action( 'wp_ajax_nopriv_bodhi_add_to_cart_v6', 'bodhi_ajax_add_to_cart_v6' );


// ── 3. SHORTCODE ───────────────────────────────────────────────────────────────
function bodhi_shop_page_v6() {

    /* ── URL params ── */
    $paged      = isset( $_GET['shop_page'] )   ? absint( $_GET['shop_page'] )                : 1;
    $per_page   = 16;
    $search     = isset( $_GET['shop_search'] ) ? sanitize_text_field( $_GET['shop_search'] ) : '';
    $cat_slug   = isset( $_GET['shop_cat'] )    ? sanitize_text_field( $_GET['shop_cat'] )    : '';
    $orderby    = isset( $_GET['shop_sort'] )   ? sanitize_text_field( $_GET['shop_sort'] )   : 'date-desc';
    $min_price  = isset( $_GET['min_price'] )   ? floatval( $_GET['min_price'] )              : 0;
    $max_price  = isset( $_GET['max_price'] )   ? floatval( $_GET['max_price'] )              : 9999;
    $min_rating = isset( $_GET['shop_rating'] ) ? intval( $_GET['shop_rating'] )              : 0;
    $base_url   = strtok( $_SERVER['REQUEST_URI'], '?' );

    /*
     * Author names from GET — normalize with html_entity_decode so Tamil
     * text round-trips correctly through URL encoding without corruption.
     */
    $sel_authors = array();
    if ( isset( $_GET['shop_authors'] ) && is_array( $_GET['shop_authors'] ) ) {
        foreach ( wp_unslash( $_GET['shop_authors'] ) as $raw ) {
            $clean = trim( wp_strip_all_tags( html_entity_decode( $raw, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
            $clean = ltrim( $clean, "-\u{2013}\u{2014}\u{2012} \t" );
            $clean = trim( $clean );
            if ( $clean !== '' ) $sel_authors[] = $clean;
        }
        $sel_authors = array_values( array_unique( $sel_authors ) );
    }

    /* ── Sort map ── */
    $sort_map = array(
        'date-desc'  => array( 'orderby' => 'date',           'order' => 'DESC', 'meta_key' => '',                   'label' => 'Default sorting'            ),
        'popularity' => array( 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => 'total_sales',        'label' => 'Sort by popularity'         ),
        'rating'     => array( 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => '_wc_average_rating', 'label' => 'Sort by average rating'     ),
        'date-asc'   => array( 'orderby' => 'date',           'order' => 'ASC',  'meta_key' => '',                   'label' => 'Sort by latest'             ),
        'price-asc'  => array( 'orderby' => 'meta_value_num', 'order' => 'ASC',  'meta_key' => '_price',             'label' => 'Sort by price: low to high' ),
        'price-desc' => array( 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => '_price',             'label' => 'Sort by price: high to low' ),
    );
    $sort = isset( $sort_map[ $orderby ] ) ? $sort_map[ $orderby ] : $sort_map['date-desc'];

    /* ── WP_Query args ── */
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'orderby'        => $sort['orderby'],
        'order'          => $sort['order'],
        'post_status'    => 'publish',
    );
    if ( ! empty( $sort['meta_key'] ) ) $args['meta_key'] = $sort['meta_key'];
    if ( $search )   $args['s'] = $search;
    if ( $cat_slug ) $args['tax_query'] = array( array(
        'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat_slug,
    ) );

    /* Price + rating meta query */
    $meta_query = array();
    if ( $min_price > 0 || $max_price < 9999 ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => array( $min_price, $max_price ),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        );
    }
    if ( $min_rating > 0 ) {
        $meta_query[] = array(
            'key'     => '_wc_average_rating',
            'value'   => $min_rating,
            'compare' => '>=',
            'type'    => 'DECIMAL',
        );
    }
    if ( count( $meta_query ) > 1 ) $meta_query['relation'] = 'AND';
    if ( ! empty( $meta_query ) )   $args['meta_query'] = $meta_query;

    /*
     * Author filter via post_excerpt.
     * Uses TRIM() on both the DB column and the value so leading/trailing
     * whitespace differences never cause a mismatch. This was the root
     * cause of "0 results" for some authors.
     */
    $excerpt_filter_fn = null;
    if ( ! empty( $sel_authors ) ) {
        $excerpt_filter_fn = function( $where ) use ( $sel_authors ) {
            global $wpdb;
            $conditions = array();
            foreach ( $sel_authors as $author ) {
                /* TRIM the DB column of spaces AND leading dashes so it matches
                 * the normalized value we have in $sel_authors */
                $conditions[] = $wpdb->prepare(
                    'TRIM(LEADING "-" FROM TRIM(' . $wpdb->posts . '.post_excerpt)) = %s',
                    $author
                );
                /* Also match exact (in case some products have no dash) */
                $conditions[] = $wpdb->prepare(
                    'TRIM(' . $wpdb->posts . '.post_excerpt) = %s',
                    $author
                );
            }
            $where .= ' AND ( ' . implode( ' OR ', $conditions ) . ' )';
            return $where;
        };
        add_filter( 'posts_where', $excerpt_filter_fn );
    }

    $query       = new WP_Query( $args );
    $total_pages = $query->max_num_pages;
    $found       = $query->found_posts;

    /* Remove filter immediately — must not affect other queries */
    if ( $excerpt_filter_fn !== null ) {
        remove_filter( 'posts_where', $excerpt_filter_fn );
    }

    /* ── Categories ── */
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'exclude'    => get_option( 'default_product_cat' ),
    ) );

    /*
     * ── Author list from post_excerpt ──
     * SELECT DISTINCT TRIM(post_excerpt) so stored values with leading/trailing
     * spaces produce the same string we compare against in the filter above.
     */
    global $wpdb;
    $raw_authors = $wpdb->get_col( $wpdb->prepare(
        "SELECT DISTINCT TRIM(post_excerpt) AS author
         FROM {$wpdb->posts}
         WHERE post_type   = %s
         AND   post_status = %s
         AND   TRIM(post_excerpt) != ''
         ORDER BY author ASC
         LIMIT 80",
        'product', 'publish'
    ) );

    /* Normalize the same way we normalize incoming GET values.
     * Also strip any leading dash/hyphen that crept into short descriptions. */
    $all_authors = array();
    $seen        = array();
    foreach ( $raw_authors as $n ) {
        $n = trim( wp_strip_all_tags( html_entity_decode( $n, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
        /* Remove leading dashes, hyphens, en-dashes, em-dashes, spaces */
        $n = ltrim( $n, "-\u{2013}\u{2014}\u{2012} \t" );
        $n = trim( $n );
        if ( $n !== '' && ! in_array( $n, $seen, true ) ) {
            $all_authors[] = $n;
            $seen[]        = $n;
        }
    }

    /* Fallback if no products have a short description yet */
    if ( empty( $all_authors ) ) {
        $all_authors = array(
            'ஓ.ரா.ந.கிருஷ்ணன்',
            'தமிழில் ஓ.ரா.ந.கிருஷ்ணன்',
            'தமிழாக்கம் ஓ.ரா.ந.கிருஷ்ணன்',
            'வி.அமலன் ஸ்டேன்லி',
            'தமிழில் வி.அமலன் ஸ்டேன்லி',
            'பேரா.முனைவர்க.ஜெயபாலன்',
            'P.Lakshmi Narasu',
        );
    }

    /* ── Price bounds ── */
    $pb = $wpdb->get_row(
        "SELECT MIN(CAST(pm.meta_value AS DECIMAL(10,2))) AS mn,
                MAX(CAST(pm.meta_value AS DECIMAL(10,2))) AS mx
         FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_price' AND pm.meta_value != ''
         AND   p.post_type = 'product' AND p.post_status = 'publish'"
    );
    $abs_min = ( $pb && $pb->mn ) ? max( 0, (int) floor( (float) $pb->mn ) ) : 60;
    $abs_max = ( $pb && $pb->mx ) ? max( $abs_min + 10, (int) ceil( (float) $pb->mx ) ) : 900;
    if ( $min_price <= 0 )    $min_price = $abs_min;
    if ( $max_price >= 9999 ) $max_price = $abs_max;

    /* ── Misc ── */
    $nonce    = wp_create_nonce( 'bodhi_atc_nonce_v6' );
    $ajax_url = admin_url( 'admin-ajax.php' );
    $cart_url = wc_get_cart_url();

    $cat_icons = array(
        'children'    => '📚', 'food'        => '🍷',
        'biography'   => '👤', 'biographies' => '👤',
        'romance'     => '❤️', 'fantasy'     => '🔮',
        'history'     => '📜', 'literature'  => '🔖',
        'sci-fi'      => '🚀', 'crime'       => '🔍',
        'travel'      => '✈️', 'health'      => '💊',
        'action'      => '⚡', 'baby'        => '🍼',
    );

    /* Pagination params (carry active filters across pages) */
    $qp = array_filter( array(
        'shop_cat'    => $cat_slug ?: null,
        'shop_search' => $search   ?: null,
        'shop_sort'   => ( $orderby !== 'date-desc' ) ? $orderby : null,
        'min_price'   => ( $min_price > $abs_min ) ? $min_price : null,
        'max_price'   => ( $max_price < $abs_max ) ? $max_price : null,
    ) );
    if ( ! empty( $sel_authors ) ) $qp['shop_authors'] = $sel_authors;

    /* ══════════════════════════════════════════════════════
       CSS
    ══════════════════════════════════════════════════════ */
    $out = '
<style>
@import url("https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap");

.bshop6{
    --ink:#1a1a2e; --ink60:#666; --ink40:#aaa;
    --accent:#4e7c59; --accent-lt:#edf4ef;
    --sale:#e8a045; --border:#e0dbd4; --bg:#f8f6f3; --white:#fff;
    --red:#c0392b; --green:#27ae60;
    --r:8px; --sh:0 1px 8px rgba(0,0,0,.06); --sh-h:0 8px 28px rgba(0,0,0,.14);
    --ease:.2s cubic-bezier(.4,0,.2,1);
    font-family:"DM Sans",sans-serif; color:var(--ink);
    background:var(--white); width:100%; box-sizing:border-box; overflow-x:hidden;
}
.bshop6 *{ box-sizing:border-box; }

/* HERO */
.bs6-hero{
    background:#f0ebe3;
    background-image:radial-gradient(ellipse at 70% 50%,rgba(78,124,89,.09) 0%,transparent 65%);
    padding:44px 40px 36px; position:relative; overflow:hidden;
}
.bs6-hero::after{ content:""; position:absolute; right:-80px; top:-80px; width:340px; height:340px; border-radius:50%; background:radial-gradient(circle,rgba(78,124,89,.1) 0%,transparent 70%); pointer-events:none; }
.bs6-hero-title{ font-family:"Lora",serif; font-size:clamp(22px,3.5vw,42px); font-weight:700; color:var(--ink); margin:0 0 6px; line-height:1.2; }
.bs6-hero-sub{ font-size:14px; color:var(--ink60); margin:0 0 24px; }
.bs6-hero-pills{ display:flex; flex-wrap:wrap; gap:10px; }
.bs6-cpill{ display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:50px; background:var(--white); border:1.5px solid var(--border); color:var(--ink); font-size:13px; font-weight:500; text-decoration:none; box-shadow:var(--sh); transition:all var(--ease); white-space:nowrap; }
.bs6-cpill-icon{ width:30px; height:30px; border-radius:50%; background:var(--bg); display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.bs6-cpill:hover{ border-color:var(--accent); color:var(--accent); box-shadow:var(--sh-h); }
.bs6-cpill:hover .bs6-cpill-icon{ background:var(--accent-lt); }
.bs6-cpill.active{ background:var(--ink); color:var(--white); border-color:var(--ink); }
.bs6-cpill.active .bs6-cpill-icon{ background:rgba(255,255,255,.18); }

/* MOBILE BAR */
.bs6-mobile-bar{ display:none; align-items:center; justify-content:space-between; padding:10px 16px; background:var(--white); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.bs6-filter-btn{ display:inline-flex; align-items:center; gap:7px; padding:8px 16px; background:var(--white); border:1.5px solid var(--border); border-radius:8px; font-family:"DM Sans",sans-serif; font-size:13px; font-weight:600; color:var(--ink); cursor:pointer; transition:all var(--ease); }
.bs6-filter-btn:hover{ border-color:var(--accent); color:var(--accent); }
.bs6-filter-btn svg{ width:16px; height:16px; flex-shrink:0; }
.bs6-mobile-sort select{ border:1.5px solid var(--border); border-radius:8px; padding:8px 28px 8px 10px; font-family:"DM Sans",sans-serif; font-size:12px; color:var(--ink); background:var(--white) url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\' viewBox=\'0 0 10 6\'%3E%3Cpath d=\'M1 1l4 4 4-4\' stroke=\'%23666\' stroke-width=\'1.5\' fill=\'none\' stroke-linecap=\'round\'/%3E%3C/svg%3E") no-repeat right 8px center; appearance:none; cursor:pointer; outline:none; }

/* LAYOUT */
.bs6-layout{ display:flex; gap:0; align-items:flex-start; min-height:600px; }

/* SIDEBAR */
.bs6-sidebar{ width:260px; min-width:240px; flex-shrink:0; padding:24px 18px 40px; border-right:1px solid var(--border); background:var(--white); position:sticky; top:0; max-height:100vh; overflow-y:auto; }
.bs6-sidebar::-webkit-scrollbar{ width:3px; }
.bs6-sidebar::-webkit-scrollbar-thumb{ background:var(--border); border-radius:3px; }
.bs6-sidebar-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; backdrop-filter:blur(2px); }
.bs6-sidebar-overlay.show{ display:block; }
.bs6-sidebar-close{ display:none; align-items:center; justify-content:space-between; margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid var(--border); }
.bs6-sidebar-close span{ font-family:"Lora",serif; font-size:16px; font-weight:700; }
.bs6-sidebar-close button{ background:var(--bg); border:none; cursor:pointer; width:30px; height:30px; border-radius:50%; font-size:16px; color:var(--ink); line-height:1; }
.bs6-widget{ margin-bottom:26px; }
.bs6-widget-title{ font-family:"Lora",serif; font-size:14px; font-weight:700; color:var(--ink); margin:0 0 12px; padding-bottom:8px; border-bottom:2px solid var(--border); }

.bs6-cat-list{ list-style:none; margin:0; padding:0; }
.bs6-cat-list li{ margin-bottom:2px; }
.bs6-cat-list a{ display:flex; align-items:center; justify-content:space-between; padding:7px 8px; border-radius:6px; text-decoration:none; font-size:13px; color:var(--ink60); transition:all var(--ease); }
.bs6-cat-list a:hover{ background:var(--bg); color:var(--ink); }
.bs6-cat-list a.active{ background:var(--accent-lt); color:var(--accent); font-weight:600; }
.bs6-cat-count{ font-size:11px; background:var(--bg); color:var(--ink40); padding:2px 7px; border-radius:50px; font-weight:500; }
.bs6-cat-list a.active .bs6-cat-count{ background:rgba(78,124,89,.15); color:var(--accent); }

.bs6-check-list{ list-style:none; margin:0; padding:0; }
.bs6-check-list li{ margin-bottom:10px; }
.bs6-check-list label{ display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13px; color:var(--ink60); line-height:1.4; transition:color var(--ease); }
.bs6-check-list label:hover{ color:var(--ink); }
.bs6-check-list input[type=checkbox]{ width:16px; height:16px; min-width:16px; border:1.5px solid var(--border); border-radius:4px; appearance:none; cursor:pointer; background:var(--white); position:relative; margin-top:2px; transition:all var(--ease); }
.bs6-check-list input[type=checkbox]:checked{ background:var(--ink); border-color:var(--ink); }
.bs6-check-list input[type=checkbox]:checked::after{ content:""; position:absolute; left:4px; top:2px; width:6px; height:9px; border:2px solid white; border-top:none; border-left:none; transform:rotate(40deg); }

.bs6-price-range{ padding:4px 0; }
.bs6-price-slider-wrap{ position:relative; height:6px; margin:18px 0 12px; }
.bs6-price-track{ position:absolute; top:0; left:0; right:0; bottom:0; background:var(--border); border-radius:3px; }
.bs6-price-fill{ position:absolute; top:0; bottom:0; background:var(--ink); border-radius:3px; }
.bs6-price-thumb{ position:absolute; top:50%; width:20px; height:20px; background:var(--white); border:2px solid var(--ink); border-radius:50%; transform:translate(-50%,-50%); cursor:grab; box-shadow:0 2px 6px rgba(0,0,0,.14); touch-action:none; }
.bs6-price-thumb:active{ cursor:grabbing; transform:translate(-50%,-50%) scale(1.2); }
.bs6-price-inputs{ display:flex; align-items:center; justify-content:space-between; margin-top:10px; }
.bs6-price-val{ display:flex; flex-direction:column; gap:2px; }
.bs6-price-val small{ font-size:10px; color:var(--ink40); text-transform:uppercase; letter-spacing:.5px; }
.bs6-price-val strong{ font-size:13px; font-weight:700; color:var(--ink); }
.bs6-price-apply{ width:100%; margin-top:12px; padding:9px; background:var(--ink); color:var(--white); border:none; cursor:pointer; border-radius:6px; font-size:13px; font-weight:600; font-family:"DM Sans",sans-serif; transition:background var(--ease); }
.bs6-price-apply:hover{ background:var(--accent); }

.bs6-rating-list{ list-style:none; margin:0; padding:0; }
.bs6-rating-list li{ margin-bottom:8px; }
.bs6-rating-list label{ display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--ink60); }
.bs6-rating-list label:hover{ color:var(--ink); }
.bs6-rating-list input[type=checkbox]{ width:16px; height:16px; min-width:16px; border:1.5px solid var(--border); border-radius:4px; appearance:none; cursor:pointer; background:var(--white); position:relative; }
.bs6-rating-list input[type=checkbox]:checked{ background:var(--ink); border-color:var(--ink); }
.bs6-rating-list input[type=checkbox]:checked::after{ content:""; position:absolute; left:4px; top:2px; width:6px; height:9px; border:2px solid white; border-top:none; border-left:none; transform:rotate(40deg); }
.bs6-stars{ display:flex; gap:2px; }
.bs6-stars span{ font-size:14px; line-height:1; color:#f59e0b; }
.bs6-stars .empty{ opacity:.22; }

/* MAIN */
.bs6-main{ flex:1; min-width:0; padding:20px 20px 60px; }
.bs6-filterbar{ display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid var(--border); }
.bs6-result-info{ display:flex; flex-wrap:wrap; align-items:center; gap:8px; }
.bs6-result-count{ font-size:13px; color:var(--ink60); }
.bs6-result-count strong{ color:var(--ink); font-weight:700; }
.bs6-active-tag{ display:inline-flex; align-items:center; gap:5px; background:var(--accent-lt); color:var(--accent); border:1px solid rgba(78,124,89,.3); border-radius:50px; padding:3px 10px; font-size:11px; font-weight:600; }
.bs6-active-tag a{ color:var(--accent); text-decoration:none; font-size:13px; font-weight:700; line-height:1; }
.bs6-top-controls{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.bs6-search-wrap{ display:flex; border:1.5px solid var(--border); border-radius:8px; overflow:hidden; background:var(--white); transition:border-color var(--ease),box-shadow var(--ease); }
.bs6-search-wrap:focus-within{ border-color:var(--accent); box-shadow:0 0 0 3px rgba(78,124,89,.1); }
.bs6-search-wrap input{ border:none; outline:none; background:transparent; padding:9px 12px; font-size:13px; font-family:"DM Sans",sans-serif; color:var(--ink); width:200px; }
.bs6-search-wrap input::placeholder{ color:var(--ink40); }
.bs6-search-wrap button{ background:var(--ink); border:none; cursor:pointer; padding:9px 14px; color:var(--white); font-size:13px; font-family:"DM Sans",sans-serif; font-weight:500; transition:background var(--ease); }
.bs6-search-wrap button:hover{ background:var(--accent); }
.bs6-sort-wrap select{ border:1.5px solid var(--border); border-radius:8px; padding:9px 30px 9px 12px; font-family:"DM Sans",sans-serif; font-size:13px; color:var(--ink); background:var(--white) url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'8\' viewBox=\'0 0 12 8\'%3E%3Cpath d=\'M1 1l5 5 5-5\' stroke=\'%23666\' stroke-width=\'1.5\' fill=\'none\' stroke-linecap=\'round\'/%3E%3C/svg%3E") no-repeat right 10px center; appearance:none; cursor:pointer; outline:none; transition:border-color var(--ease); }
.bs6-sort-wrap select:focus{ border-color:var(--accent); }

/* GRID */
.bs6-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }

/* CARD */
.bs6-card{ background:var(--white); border-radius:var(--r); border:1px solid var(--border); overflow:hidden; display:flex; flex-direction:column; transition:transform var(--ease),box-shadow var(--ease); }
.bs6-card:hover{ transform:translateY(-4px); box-shadow:var(--sh-h); }
.bs6-cover{ position:relative; width:100%; aspect-ratio:3/4; overflow:hidden; background:#e8e2d9; flex-shrink:0; }
.bs6-cover img{ width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s ease; }
.bs6-card:hover .bs6-cover img{ transform:scale(1.04); }
.bs6-cover-ph{ width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:52px; opacity:.2; }
.bs6-badge{ position:absolute; top:8px; left:8px; font-size:10px; font-weight:700; padding:4px 10px; border-radius:4px; z-index:2; }
.bs6-badge--sale{ background:var(--sale); color:var(--white); }
.bs6-badge--new{ background:var(--ink); color:var(--white); }
.bs6-card-body{ padding:10px 10px 6px; flex:1; }
.bs6-card-title{ font-family:"Lora",serif; font-size:13px; font-weight:600; color:var(--ink); margin:0 0 4px; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.bs6-card-title a{ text-decoration:none; color:inherit; }
.bs6-card-title a:hover{ color:var(--accent); }

/* Author name on card — small muted text */
.bs6-card-author{ font-size:11px; color:var(--ink60); margin:0 0 5px; display:block; }

.bs6-card-stars{ display:flex; align-items:center; gap:3px; margin-bottom:5px; }
.bs6-card-stars .bs6-star{ font-size:12px; line-height:1; color:#f59e0b; }
.bs6-card-stars .bs6-star.empty{ opacity:.2; }
.bs6-card-stars .bs6-rcount{ font-size:10px; color:var(--ink40); }
.bs6-card-price{ font-size:14px; font-weight:700; color:var(--ink); }
.bs6-card-price del{ font-size:11px; color:var(--ink40); font-weight:400; margin-right:4px; }
.bs6-card-price ins{ text-decoration:none; color:var(--red); }
.bs6-card-actions{ padding:8px 10px 10px; display:flex; flex-direction:column; gap:7px; margin-top:auto; }
.bs6-atc{ display:flex; align-items:center; justify-content:center; gap:6px; width:100%; background:var(--ink); color:var(--white); font-family:"DM Sans",sans-serif; font-size:12px; font-weight:600; padding:9px; border-radius:6px; border:none; cursor:pointer; text-decoration:none; transition:background var(--ease),transform var(--ease); }
.bs6-atc:hover{ background:var(--accent); color:var(--white); transform:translateY(-1px); }
.bs6-atc.loading{ background:#52525b !important; pointer-events:none; }
.bs6-atc.added  { background:var(--green) !important; pointer-events:none; }
.bs6-atc.error  { background:var(--red) !important; pointer-events:none; }
.bs6-vcart{ display:none; width:100%; text-align:center; font-family:"DM Sans",sans-serif; font-size:12px; font-weight:600; color:var(--accent); text-decoration:none; padding:7px 0; border:1.5px solid var(--accent); border-radius:6px; background:var(--accent-lt); transition:all var(--ease); }
.bs6-vcart.show{ display:block; }
.bs6-vcart:hover{ background:var(--accent); color:var(--white); }

.bs6-empty{ grid-column:1/-1; text-align:center; padding:60px 20px; }
.bs6-empty-icon{ font-size:48px; margin-bottom:12px; }
.bs6-empty-h{ font-family:"Lora",serif; font-size:20px; margin:0 0 8px; }
.bs6-empty-p{ font-size:14px; color:var(--ink60); }
.bs6-pag{ display:flex; justify-content:center; flex-wrap:wrap; gap:6px; padding-top:36px; }
.bs6-pag-a{ min-width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; padding:0 10px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; border:1.5px solid var(--border); color:var(--ink60); background:var(--white); transition:all var(--ease); }
.bs6-pag-a:hover{ border-color:var(--ink); color:var(--ink); }
.bs6-pag-a.on{ background:var(--ink); color:var(--white); border-color:var(--ink); }
.bs6-toast{ position:fixed; bottom:20px; right:20px; z-index:9999; background:var(--ink); color:var(--white); padding:12px 20px; border-radius:50px; font-family:"DM Sans",sans-serif; font-size:13px; font-weight:600; box-shadow:0 6px 24px rgba(0,0,0,.22); display:none; align-items:center; gap:10px; text-decoration:none; }
.bs6-toast.show{ display:flex; }

/* ── RESPONSIVE ── */
@media(max-width:1280px){ .bs6-grid{ grid-template-columns:repeat(3,1fr); } }
@media(max-width:1024px){
    .bs6-hero{ padding:36px 24px 28px; }
    .bs6-sidebar{ width:230px; min-width:210px; }
    .bs6-main{ padding:16px 16px 48px; }
    .bs6-grid{ grid-template-columns:repeat(3,1fr); gap:14px; }
}
@media(max-width:768px){
    .bs6-mobile-bar{ display:flex; }
    .bs6-sidebar{ position:fixed; top:0; left:0; bottom:0; width:290px; max-height:100vh; z-index:300; transform:translateX(-100%); box-shadow:4px 0 24px rgba(0,0,0,.15); padding:20px 18px 40px; transition:transform .28s cubic-bezier(.4,0,.2,1); }
    .bs6-sidebar.open{ transform:translateX(0); }
    .bs6-sidebar-close{ display:flex; }
    .bs6-hero{ padding:28px 16px 22px; }
    .bs6-hero-title{ font-size:clamp(20px,5vw,30px); }
    .bs6-hero-sub{ margin-bottom:16px; font-size:13px; }
    .bs6-hero-pills{ flex-wrap:nowrap; overflow-x:auto; padding-bottom:4px; gap:8px; -webkit-overflow-scrolling:touch; scrollbar-width:none; }
    .bs6-hero-pills::-webkit-scrollbar{ display:none; }
    .bs6-cpill{ padding:8px 14px; font-size:12px; flex-shrink:0; }
    .bs6-cpill-icon{ width:26px; height:26px; font-size:13px; }
    .bs6-layout{ flex-direction:column; }
    .bs6-main{ padding:14px 14px 40px; width:100%; }
    .bs6-grid{ grid-template-columns:repeat(2,1fr); gap:12px; }
    .bs6-filterbar{ flex-direction:column; align-items:flex-start; gap:8px; }
    .bs6-top-controls{ width:100%; justify-content:space-between; }
    .bs6-search-wrap{ flex:1; }
    .bs6-search-wrap input{ width:100%; }
    .bs6-sort-wrap{ display:none; }
    .bs6-card-body{ padding:8px 8px 4px; }
    .bs6-card-title{ font-size:12px; }
    .bs6-card-price{ font-size:13px; }
    .bs6-card-actions{ padding:6px 8px 8px; gap:5px; }
    .bs6-atc{ font-size:11px; padding:8px; }
    .bs6-vcart{ font-size:11px; padding:6px 0; }
}
@media(max-width:480px){
    .bs6-hero{ padding:20px 14px 18px; }
    .bs6-hero-title{ font-size:clamp(18px,6vw,24px); }
    .bs6-mobile-bar{ padding:8px 12px; }
    .bs6-filter-btn{ padding:7px 12px; font-size:12px; }
    .bs6-main{ padding:10px 10px 32px; }
    .bs6-grid{ grid-template-columns:repeat(2,1fr); gap:10px; }
    .bs6-cover{ aspect-ratio:2/3; }
    .bs6-card-body{ padding:6px 6px 2px; }
    .bs6-card-title{ font-size:11px; }
    .bs6-card-price{ font-size:12px; }
    .bs6-card-actions{ padding:5px 6px 7px; gap:4px; }
    .bs6-atc{ font-size:11px; padding:7px 6px; border-radius:5px; }
    .bs6-vcart{ font-size:10px; padding:5px 0; border-radius:5px; }
    .bs6-pag-a{ min-width:34px; height:34px; font-size:12px; }
    .bs6-toast{ bottom:14px; right:12px; padding:10px 14px; font-size:12px; border-radius:40px; }
}
@media(max-width:360px){
    .bs6-grid{ grid-template-columns:1fr; }
    .bs6-card{ flex-direction:row; height:110px; }
    .bs6-cover{ width:80px; min-width:80px; aspect-ratio:auto; height:110px; flex-shrink:0; }
    .bs6-card-body{ padding:8px 8px 4px; }
    .bs6-card-title{ font-size:12px; }
    .bs6-card-actions{ padding:4px 8px 8px; }
}
</style>';


    /* ══════════════════════════════════════════════════════
       JAVASCRIPT
    ══════════════════════════════════════════════════════ */
    $out .= '
<script>
(function(){
    var AJ      = ' . json_encode( $ajax_url ) . ';
    var NK      = ' . json_encode( $nonce )    . ';
    var ABS_MIN = ' . (int) $abs_min            . ';
    var ABS_MAX = ' . (int) $abs_max            . ';
    var curMin  = ' . (int) $min_price          . ';
    var curMax  = ' . (int) $max_price          . ';

    /* SORT */
    document.addEventListener("change", function(e){
        if (!e.target.classList.contains("bs6-sort-sel")) return;
        var u = new URL(window.location.href);
        u.searchParams.set("shop_sort", e.target.value);
        u.searchParams.delete("shop_page");
        window.location.href = u.toString();
    });

    /* SIDEBAR DRAWER */
    function openSidebar(){
        var sb=document.querySelector(".bs6-sidebar"), ov=document.querySelector(".bs6-sidebar-overlay");
        if(sb) sb.classList.add("open");
        if(ov) ov.classList.add("show");
        document.body.style.overflow="hidden";
    }
    function closeSidebar(){
        var sb=document.querySelector(".bs6-sidebar"), ov=document.querySelector(".bs6-sidebar-overlay");
        if(sb) sb.classList.remove("open");
        if(ov) ov.classList.remove("show");
        document.body.style.overflow="";
    }
    document.addEventListener("click", function(e){
        if(e.target.closest(".bs6-filter-btn")) openSidebar();
        if(e.target.closest(".bs6-sidebar-close button")) closeSidebar();
        if(e.target.classList.contains("bs6-sidebar-overlay")) closeSidebar();
    });

    /* ADD TO CART */
    document.addEventListener("click", function(e){
        var btn = e.target.closest(".bs6-atc[data-pid]");
        if(!btn) return;
        e.preventDefault();
        if(btn.classList.contains("loading")) return;

        var pid   = btn.dataset.pid;
        var card  = btn.closest(".bs6-card");
        var vc    = card ? card.querySelector(".bs6-vcart") : null;
        var toast = document.querySelector(".bs6-toast");

        btn.classList.add("loading");
        btn.textContent = "Adding\u2026";

        var fd = new FormData();
        fd.append("action",     "bodhi_add_to_cart_v6");
        fd.append("nonce",      NK);
        fd.append("product_id", pid);

        fetch(AJ, {method:"POST", body:fd, credentials:"same-origin"})
            .then(function(r){ return r.json(); })
            .then(function(d){
                btn.classList.remove("loading");
                if(d.success){
                    btn.classList.add("added");
                    btn.textContent = "\u2713 Added to Cart";
                    if(vc){ vc.href = d.data.cart_url; vc.classList.add("show"); }
                    if(toast){
                        toast.href      = d.data.cart_url;
                        toast.innerHTML = "\uD83D\uDED2 Cart (" + d.data.cart_count + ") \u2192 View Cart";
                        toast.classList.add("show");
                        clearTimeout(toast._t);
                        toast._t = setTimeout(function(){ toast.classList.remove("show"); }, 4000);
                    }
                    setTimeout(function(){ btn.classList.remove("added"); btn.textContent = "Add to Cart"; }, 4000);
                } else {
                    btn.classList.add("error");
                    btn.textContent = "Error \u2013 try again";
                    setTimeout(function(){ btn.classList.remove("error"); btn.textContent = "Add to Cart"; }, 3000);
                }
            })
            .catch(function(){ btn.classList.remove("loading"); btn.textContent = "Add to Cart"; });
    });

    /* PRICE SLIDER */
    document.addEventListener("DOMContentLoaded", function(){
        var track  = document.querySelector(".bs6-price-track");
        var fill   = document.querySelector(".bs6-price-fill");
        var thumbL = document.querySelector(".bs6-price-thumb-l");
        var thumbR = document.querySelector(".bs6-price-thumb-r");
        var valL   = document.querySelector(".bs6-val-min");
        var valR   = document.querySelector(".bs6-val-max");
        var inpMin = document.getElementById("bs6-inp-min");
        var inpMax = document.getElementById("bs6-inp-max");
        if(!track || !thumbL || !thumbR) return;

        function pct(v){ return (v - ABS_MIN) / (ABS_MAX - ABS_MIN) * 100; }
        function fromPct(p){ return Math.round(ABS_MIN + p / 100 * (ABS_MAX - ABS_MIN)); }
        function redraw(){
            var p1 = pct(curMin), p2 = pct(curMax);
            fill.style.left   = p1 + "%";
            fill.style.right  = (100 - p2) + "%";
            thumbL.style.left = p1 + "%";
            thumbR.style.left = p2 + "%";
            if(valL)  valL.textContent  = "\u20b9" + curMin;
            if(valR)  valR.textContent  = "\u20b9" + curMax;
            if(inpMin) inpMin.value = curMin;
            if(inpMax) inpMax.value = curMax;
        }
        redraw();

        function drag(thumb, isLeft){
            function onMove(cx){
                var r = track.getBoundingClientRect();
                var p = Math.max(0, Math.min(100, (cx - r.left) / r.width * 100));
                if(isLeft) curMin = Math.min(fromPct(p), curMax - 1);
                else       curMax = Math.max(fromPct(p), curMin + 1);
                redraw();
            }
            thumb.addEventListener("mousedown", function(e){
                e.preventDefault();
                function mm(ev){ onMove(ev.clientX); }
                function mu(){ document.removeEventListener("mousemove", mm); document.removeEventListener("mouseup", mu); }
                document.addEventListener("mousemove", mm);
                document.addEventListener("mouseup",   mu);
            });
            thumb.addEventListener("touchstart", function(e){
                e.preventDefault();
                function tm(ev){ onMove(ev.touches[0].clientX); }
                function tu(){ document.removeEventListener("touchmove", tm); document.removeEventListener("touchend", tu); }
                document.addEventListener("touchmove", tm, {passive:false});
                document.addEventListener("touchend",  tu);
            }, {passive:false});
        }
        drag(thumbL, true);
        drag(thumbR, false);
    });
})();
</script>';


    /* ══════════════════════════════════════════════════════
       HTML
    ══════════════════════════════════════════════════════ */
    $out .= '<a href="' . esc_url( $cart_url ) . '" class="bs6-toast"></a>';
    $out .= '<div class="bs6-sidebar-overlay"></div>';
    $out .= '<div class="bshop6">';

    /* HERO */
    $all_href = add_query_arg( array_filter( array(
        'shop_search' => $search  ?: null,
        'shop_sort'   => ( $orderby !== 'date-desc' ) ? $orderby : null,
    ) ), $base_url );
    $out .= '<div class="bs6-hero">'
          . '<h1 class="bs6-hero-title">போதிமுரசு புத்தகக் கடை</h1>'
          . '<p class="bs6-hero-sub"></p>'
          . '<div class="bs6-hero-pills">'
          . '<a href="' . esc_url( $all_href ) . '" class="bs6-cpill' . ( ! $cat_slug ? ' active' : '' ) . '"><span class="bs6-cpill-icon">★</span>All Books</a>';

    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        foreach ( $categories as $cat ) {
            $icon = '📖';
            foreach ( $cat_icons as $k => $v ) { if ( strpos( $cat->slug, $k ) !== false ) { $icon = $v; break; } }
            $href = add_query_arg( array_filter( array(
                'shop_cat'    => $cat->slug,
                'shop_search' => $search ?: null,
                'shop_sort'   => ( $orderby !== 'date-desc' ) ? $orderby : null,
            ) ), $base_url );
            $out .= '<a href="' . esc_url( $href ) . '" class="bs6-cpill' . ( $cat_slug === $cat->slug ? ' active' : '' ) . '"><span class="bs6-cpill-icon">' . $icon . '</span>' . esc_html( $cat->name ) . '</a>';
        }
    }
    $out .= '</div></div>';

    /* MOBILE BAR */
    $badge = ! empty( $sel_authors )
        ? ' <span style="background:var(--accent);color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-left:2px;">' . count( $sel_authors ) . '</span>'
        : '';
    $out .= '<div class="bs6-mobile-bar">'
          . '<button class="bs6-filter-btn" type="button"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 5h14M6 10h8M9 15h2"/></svg> Filters' . $badge . '</button>'
          . '<div class="bs6-mobile-sort"><select class="bs6-sort-sel">';
    foreach ( $sort_map as $val => $info ) {
        $out .= '<option value="' . esc_attr( $val ) . '"' . ( $val === $orderby ? ' selected' : '' ) . '>' . esc_html( $info['label'] ) . '</option>';
    }
    $out .= '</select></div></div>';

    $out .= '<div class="bs6-layout"><aside class="bs6-sidebar">';
    $out .= '<div class="bs6-sidebar-close"><span>Filters</span><button type="button">✕</button></div>';

    /* CATEGORIES */
    $out .= '<div class="bs6-widget"><h3 class="bs6-widget-title">Categories</h3><ul class="bs6-cat-list">';
    $out .= '<li><a href="' . esc_url( $all_href ) . '"' . ( ! $cat_slug ? ' class="active"' : '' ) . '>All Books<span class="bs6-cat-count">(' . (int) $found . ')</span></a></li>';
    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        foreach ( $categories as $cat ) {
            $href = add_query_arg( array_filter( array(
                'shop_cat'    => $cat->slug,
                'shop_search' => $search ?: null,
                'shop_sort'   => ( $orderby !== 'date-desc' ) ? $orderby : null,
            ) ), $base_url );
            $out .= '<li><a href="' . esc_url( $href ) . '"' . ( $cat_slug === $cat->slug ? ' class="active"' : '' ) . '>' . esc_html( $cat->name ) . '<span class="bs6-cat-count">(' . (int) $cat->count . ')</span></a></li>';
        }
    }
    $out .= '</ul></div>';

    /* AUTHOR filter */
    $out .= '<div class="bs6-widget"><h3 class="bs6-widget-title">ஆசிரியர்</h3>';
    if ( ! empty( $sel_authors ) ) {
        $clear_url = add_query_arg( array_filter( array(
            'shop_cat'    => $cat_slug ?: null,
            'shop_search' => $search   ?: null,
            'shop_sort'   => ( $orderby !== 'date-desc' ) ? $orderby : null,
        ) ), $base_url );
        $out .= '<div style="margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;">'
              . '<span style="font-size:11px;color:var(--accent);font-weight:600;">' . count( $sel_authors ) . ' selected</span>'
              . '<a href="' . esc_url( $clear_url ) . '" style="font-size:11px;color:var(--red);text-decoration:none;font-weight:600;">✕ Clear</a>'
              . '</div>';
    }
    $out .= '<form method="GET" action="" id="bs6-author-form" accept-charset="UTF-8">';
    if ( $cat_slug ) $out .= '<input type="hidden" name="shop_cat"    value="' . esc_attr( $cat_slug ) . '">';
    if ( $search )   $out .= '<input type="hidden" name="shop_search" value="' . esc_attr( $search ) . '">';
    if ( $orderby !== 'date-desc' ) $out .= '<input type="hidden" name="shop_sort" value="' . esc_attr( $orderby ) . '">';
    $out .= '<ul class="bs6-check-list">';
    foreach ( $all_authors as $a ) {
        $checked  = in_array( $a, $sel_authors, true ) ? ' checked' : '';
        $li_style = in_array( $a, $sel_authors, true ) ? ' style="background:var(--accent-lt);border-radius:5px;padding:2px 5px;"' : '';
        $out .= '<li' . $li_style . '><label>'
              . '<input type="checkbox" name="shop_authors[]" value="' . esc_attr( $a ) . '"' . $checked . ' onchange="this.form.submit()">'
              . esc_html( $a )
              . '</label></li>';
    }
    $out .= '</ul><noscript><button type="submit" class="bs6-price-apply">Apply</button></noscript></form></div>';

    /* PRICE */
    $out .= '<div class="bs6-widget"><h3 class="bs6-widget-title">Filter by Price</h3>'
          . '<div class="bs6-price-range"><div class="bs6-price-slider-wrap">'
          . '<div class="bs6-price-track"></div><div class="bs6-price-fill"></div>'
          . '<div class="bs6-price-thumb bs6-price-thumb-l" role="slider"></div>'
          . '<div class="bs6-price-thumb bs6-price-thumb-r" role="slider"></div>'
          . '</div><div class="bs6-price-inputs">'
          . '<div class="bs6-price-val"><small>Min. Price</small><strong class="bs6-val-min">₹' . (int) $min_price . '</strong></div>'
          . '<div class="bs6-price-val" style="text-align:right"><small>Max. Price</small><strong class="bs6-val-max">₹' . (int) $max_price . '</strong></div>'
          . '</div><form method="GET" action="" id="bs6-price-form">';
    if ( $cat_slug ) $out .= '<input type="hidden" name="shop_cat"    value="' . esc_attr( $cat_slug ) . '">';
    if ( $search )   $out .= '<input type="hidden" name="shop_search" value="' . esc_attr( $search ) . '">';
    if ( $orderby !== 'date-desc' ) $out .= '<input type="hidden" name="shop_sort" value="' . esc_attr( $orderby ) . '">';
    foreach ( $sel_authors as $a ) $out .= '<input type="hidden" name="shop_authors[]" value="' . esc_attr( $a ) . '">';
    $out .= '<input type="hidden" id="bs6-inp-min" name="min_price" value="' . (int) $min_price . '">'
          . '<input type="hidden" id="bs6-inp-max" name="max_price" value="' . (int) $max_price . '">'
          . '<button type="submit" class="bs6-price-apply">Apply Price</button>'
          . '</form></div></div>';

    /* RATING */
    $out .= '<div class="bs6-widget"><h3 class="bs6-widget-title">Rating</h3>'
          . '<form method="GET" action="" id="bs6-rating-form">';
    if ( $cat_slug ) $out .= '<input type="hidden" name="shop_cat"    value="' . esc_attr( $cat_slug ) . '">';
    if ( $search )   $out .= '<input type="hidden" name="shop_search" value="' . esc_attr( $search ) . '">';
    if ( $orderby !== 'date-desc' ) $out .= '<input type="hidden" name="shop_sort" value="' . esc_attr( $orderby ) . '">';
    foreach ( $sel_authors as $a ) $out .= '<input type="hidden" name="shop_authors[]" value="' . esc_attr( $a ) . '">';
    $out .= '<ul class="bs6-rating-list">';
    for ( $r = 5; $r >= 1; $r-- ) {
        $chk   = ( $min_rating === $r ) ? ' checked' : '';
        $stars = str_repeat( '<span>★</span>', $r ) . str_repeat( '<span class="empty">★</span>', 5 - $r );
        $out  .= '<li><label><input type="checkbox" name="shop_rating" value="' . $r . '"' . $chk
               . ' onchange="document.getElementById(\'bs6-rating-form\').submit()">'
               . '<span class="bs6-stars">' . $stars . '</span>'
               . ( $r < 5 ? '<small style="font-size:11px;color:#999">& up</small>' : '' )
               . '</label></li>';
    }
    $out .= '</ul></form></div>';
    $out .= '</aside>';

    /* MAIN */
    $out .= '<main class="bs6-main">';

    /* FILTER BAR */
    $out .= '<div class="bs6-filterbar"><div class="bs6-result-info">'
          . '<span class="bs6-result-count">Showing <strong>' . number_format( $found ) . ' ' . _n( 'result', 'results', $found ) . '</strong>'
          . ( $search ? ' for &ldquo;<strong>' . esc_html( $search ) . '</strong>&rdquo;' : '' ) . '</span>';
    if ( ! empty( $sel_authors ) ) {
        foreach ( $sel_authors as $active_a ) {
            $rem     = array_values( array_filter( $sel_authors, function( $x ) use ( $active_a ) { return $x !== $active_a; } ) );
            $rem_url = add_query_arg( array_filter( array(
                'shop_cat'     => $cat_slug ?: null,
                'shop_search'  => $search   ?: null,
                'shop_sort'    => ( $orderby !== 'date-desc' ) ? $orderby : null,
                'shop_authors' => ! empty( $rem ) ? $rem : null,
            ) ), $base_url );
            $out .= '<span class="bs6-active-tag">' . esc_html( $active_a ) . '<a href="' . esc_url( $rem_url ) . '">✕</a></span>';
        }
    }
    $out .= '</div><div class="bs6-top-controls"><form class="bs6-search-wrap" method="GET" action="">';
    if ( $cat_slug ) $out .= '<input type="hidden" name="shop_cat" value="' . esc_attr( $cat_slug ) . '">';
    if ( $orderby !== 'date-desc' ) $out .= '<input type="hidden" name="shop_sort" value="' . esc_attr( $orderby ) . '">';
    $out .= '<input type="text" name="shop_search" placeholder="Search books..." value="' . esc_attr( $search ) . '">'
          . '<button type="submit">Search</button></form>'
          . '<div class="bs6-sort-wrap"><select class="bs6-sort-sel">';
    foreach ( $sort_map as $val => $info ) {
        $out .= '<option value="' . esc_attr( $val ) . '"' . ( $val === $orderby ? ' selected' : '' ) . '>' . esc_html( $info['label'] ) . '</option>';
    }
    $out .= '</select></div></div></div>';

    /* GRID */
    $out .= '<div class="bs6-grid">';
    if ( $query->have_posts() ) {
        $idx = 0;
        while ( $query->have_posts() ) {
            $query->the_post(); $idx++;
            global $product;
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;

            $pid        = get_the_ID();
            $title      = get_the_title();
            $link       = get_the_permalink();
            $thumb      = get_the_post_thumbnail_url( $pid, 'large' );
            $price_html = $product->get_price_html();
            $on_sale    = $product->is_on_sale();
            $is_new     = ( $paged === 1 && $idx <= 4 );
            $is_simple  = $product->is_type( 'simple' );
            $avg_rating = floatval( $product->get_average_rating() );
            $rating_cnt = $product->get_rating_count();
            /* Author stored in short description — strip leading dash before display */
            $author = trim( wp_strip_all_tags( get_post_field( 'post_excerpt', $pid ) ) );
            $author = ltrim( $author, "-\u{2013}\u{2014}\u{2012} \t" );
            $author = trim( $author );

            $out .= '<div class="bs6-card"><div class="bs6-cover">';
            $out .= $thumb
                  ? '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">'
                  : '<div class="bs6-cover-ph">📚</div>';
            if     ( $on_sale ) $out .= '<span class="bs6-badge bs6-badge--sale">Sale!</span>';
            elseif ( $is_new  ) $out .= '<span class="bs6-badge bs6-badge--new">New</span>';
            $out .= '</div><div class="bs6-card-body">';
            $out .= '<h2 class="bs6-card-title"><a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a></h2>';
            /* Hidden — exists in DOM for SEO but not visible */
            if ( $author ) $out .= '<p class="bs6-card-author">' . esc_html( $author ) . '</p>';

            $full = (int) floor( $avg_rating ); $empty = 5 - $full;
            $out .= '<div class="bs6-card-stars">';
            for ( $s = 0; $s < $full;  $s++ ) $out .= '<span class="bs6-star">★</span>';
            for ( $s = 0; $s < $empty; $s++ ) $out .= '<span class="bs6-star empty">★</span>';
            if ( $rating_cnt ) $out .= '<span class="bs6-rcount">(' . $rating_cnt . ')</span>';
            $out .= '</div>';
            $out .= '<div class="bs6-card-price">' . $price_html . '</div>';
            $out .= '</div><div class="bs6-card-actions">';
            if ( $is_simple ) {
                $out .= '<button class="bs6-atc" data-pid="' . esc_attr( $pid ) . '">Add to Cart</button>';
                $out .= '<a href="' . esc_url( $cart_url ) . '" class="bs6-vcart">View Cart &rarr;</a>';
            } else {
                $out .= '<a href="' . esc_url( $link ) . '" class="bs6-atc">Select Options &rarr;</a>';
            }
            $out .= '</div></div>';
        }
        wp_reset_postdata();
    } else {
        $out .= '<div class="bs6-empty"><div class="bs6-empty-icon">📚</div>'
              . '<h3 class="bs6-empty-h">No books found</h3>'
              . '<p class="bs6-empty-p">Try a different search or category.</p></div>';
    }
    $out .= '</div>';

    /* PAGINATION */
    if ( $total_pages > 1 ) {
        $out .= '<div class="bs6-pag">';
        if ( $paged > 1 )
            $out .= '<a href="' . esc_url( add_query_arg( array_merge( $qp, array( 'shop_page' => $paged - 1 ) ), $base_url ) ) . '" class="bs6-pag-a">← Prev</a>';
        for ( $i = 1; $i <= $total_pages; $i++ )
            $out .= '<a href="' . esc_url( add_query_arg( array_merge( $qp, array( 'shop_page' => $i ) ), $base_url ) ) . '" class="bs6-pag-a' . ( $i === $paged ? ' on' : '' ) . '">' . $i . '</a>';
        if ( $paged < $total_pages )
            $out .= '<a href="' . esc_url( add_query_arg( array_merge( $qp, array( 'shop_page' => $paged + 1 ) ), $base_url ) ) . '" class="bs6-pag-a">Next &rarr;</a>';
        $out .= '</div>';
    }

    $out .= '</main></div></div>';
    return $out;
}
add_shortcode( 'bodhi_shop', 'bodhi_shop_page_v6' );