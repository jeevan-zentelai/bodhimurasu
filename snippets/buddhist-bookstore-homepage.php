<?php

// BUDDHIST BOOKSTORE - HOMEPAGE OVERRIDE
function buddhist_bookstore_homepage() {
   if (!is_page('books')) return;

    $mag_url = home_url('/magazine/');

    // ✅ ALL SHOP LINKS → /shop/
    $shop_url = home_url('/shop');

    // Get featured/latest books from WooCommerce
    $featured_books = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'exclude-from-catalog',
            'operator' => 'NOT IN',
        )),
    ));

    // Get bestsellers
    $bestsellers = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ));

    // Cart and account URLs
    $cart_url    = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/';
    $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '/my-account/';
    $cart_count  = function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;
    $logo_url    = 'https://bodhimurasu.org/wp-content/uploads/logo.png';

    ob_start();
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - புத்த மத நூல்கள்</title>
    <?php wp_head(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400;1,700&family=Noto+Serif+Tamil:wght@400;600;700&family=Noto+Sans+Tamil:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold:    #c9a84c;
            --gold2:   #f0d080;
            --dark:    #0f0e0b;
            --dark2:   #1a1810;
            --dark3:   #252318;
            --cream:   #faf8fd;
            --cream2:  #faf8fd;
            --rust:    #8b3a1a;
            --saffron: #e8890c;
            --text:    #2a2318;
            --muted:   #7a6f5e;
            --lav-700: #2d1b4e;
            --lav-800: #4a2d73;
            --lav-950: #2d1b4e;

            --font-heading:  'Playfair Display', Georgia, serif;
            --font-tamil:    'Noto Serif Tamil', Latha, 'Nirmala UI', 'Tamil MN', serif;
            --font-ui-tamil: 'Noto Sans Tamil', 'Nirmala UI', 'Tamil MN', sans-serif;
        }
        * { margin:0; padding:0; box-sizing:border-box; }

        body { font-family:var(--font-tamil); bac  color:var(--text); overflow-x:hidden; background-color  }

        /* ── TICKER ── */
        .bk-ticker { background:var(--gold); color:var(--dark); font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:8px 0; overflow:hidden; white-space:nowrap; font-family:var(--font-ui-tamil); }
        .bk-ticker-inner { display:inline-block; animation:ticker 30s linear infinite; }
        @keyframes ticker { 0%{transform:translateX(100vw)} 100%{transform:translateX(-100%)} }

        /* ── HEADER ── */
        .bk-topbar { background:#0d0d0d; padding:0 60px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:9999; border-bottom:1px solid rgba(201,168,76,0.2); height:75px; box-shadow:0 2px 20px rgba(0,0,0,0.5); }
        .bk-logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .bk-logo img { height:45px; width:auto; object-fit:contain; }
        .bk-logo-text { display:flex; flex-direction:column; line-height:1.2; }
        .bk-logo-name { font-family:var(--font-heading); font-size:18px; font-weight:800; color:var(--gold); letter-spacing:3px; }
        .bk-logo-tamil { font-family:var(--font-tamil); font-size:11px; color:rgba(201,168,76,0.6); }
        .bk-nav { display:flex; align-items:center; gap:35px; list-style:none; margin:0; padding:0; }
        .bk-nav a { color:rgba(255,255,255,0.75); text-decoration:none; font-size:14px; letter-spacing:1px; transition:color 0.2s; position:relative; padding-bottom:4px; font-family:var(--font-ui-tamil); }
        .bk-nav a::after { content:''; position:absolute; bottom:0; left:0; width:0; height:1px; background:var(--gold); transition:width 0.3s; }
        .bk-nav a:hover { color:var(--gold); }
        .bk-nav a:hover::after { width:100%; }
        .bk-nav-actions { display:flex; align-items:center; gap:18px; }
        .bk-nav-actions a { color:rgba(255,255,255,0.7); text-decoration:none; transition:color 0.2s; position:relative; display:flex; align-items:center; font-family:var(--font-ui-tamil); }
        .bk-nav-actions a:hover { color:var(--gold); }
        .bk-cart-count { position:absolute; top:-6px; right:-8px; background:var(--saffron); color:white; font-size:9px; font-weight:800; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
        .bk-hamburger { display:none; flex-direction:column; gap:5px; cursor:pointer; padding:5px; }
        .bk-hamburger span { display:block; width:24px; height:2px; background:white; }
        .bk-mobile-nav { display:none; background:#111; padding:20px 30px; border-top:1px solid rgba(255,255,255,0.1); position:sticky; top:75px; z-index:9998; }
        .bk-mobile-nav a { display:block; color:rgba(255,255,255,0.85); text-decoration:none; font-size:15px; font-weight:600; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.08); font-family:var(--font-ui-tamil); }
        .bk-mobile-nav a:hover { color:var(--gold); }

        /* ── HERO — reduced height ── */
        .bk-hero {
            background: linear-gradient(135deg, var(--lav-950) 0%, var(--lav-800) 45%, var(--lav-700) 100%);
			display: flex;
    		justify-content: center;
            
        }
		.bk-width {
			max-width : 1200px;
			min-height: 52vh;
            display:grid;
            grid-template-columns:1fr 1fr;
            position:relative;
            overflow:hidden;
			width: 100%;
		}
        .bk-hero::before { content:''; position:absolute; top:0; left:0; right:0; bottom:0; background:radial-gradient(ellipse at 20% 50%,rgba(201,168,76,0.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(139,58,26,0.12) 0%,transparent 50%); pointer-events:none; }
        .bk-hero-mandala { position:absolute; top:50%; right:48%; transform:translate(50%,-50%); width:500px; height:500px; opacity:0.04; font-size:500px; line-height:1; user-select:none; animation:rotateSlow 60s linear infinite; }
        @keyframes rotateSlow { from{transform:translate(50%,-50%) rotate(0deg)} to{transform:translate(50%,-50%) rotate(360deg)} }

        .bk-hero-left {
            padding: 50px 0px 50px 0px;
            display:flex; flex-direction:column; justify-content:center; position:relative; z-index:2;
        }
        .bk-hero-tag { display:inline-flex; align-items:center; gap:8px; background:rgba(201,168,76,0.12); border:1px solid rgba(201,168,76,0.3); color:var(--gold); font-size:11px; letter-spacing:3px; text-transform:uppercase; padding:6px 16px; border-radius:30px; margin-bottom:18px; width:fit-content; font-family:var(--font-ui-tamil); }
        .bk-hero-title { font-family:var(--font-tamil); font-size:46px; font-weight:700; color:white; line-height:1.25; margin-bottom:8px; }
        .bk-hero-title span { color:var(--gold); }
        .bk-hero-subtitle { font-family:var(--font-heading); font-size:12px; color:rgba(255,255,255,0.35); letter-spacing:4px; text-transform:uppercase; margin-bottom:18px; }
        .bk-hero-desc { font-family:var(--font-tamil); font-size:15px; color:rgba(255,255,255,0.6); line-height:1.75; max-width:460px; margin-bottom:30px; }
        .bk-hero-btns { display:flex; gap:16px; flex-wrap:wrap; }
        .bk-btn-primary { display:inline-flex; align-items:center; gap:10px; background:var(--gold); color:var(--dark); padding:13px 30px; text-decoration:none; border-radius:4px; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; transition:all 0.3s; box-shadow:0 4px 25px rgba(201,168,76,0.35); font-family:var(--font-ui-tamil); }
        .bk-btn-primary:hover { background:var(--gold2); transform:translateY(-2px); }
        .bk-btn-outline { display:inline-flex; align-items:center; gap:10px; background:transparent; color:white; padding:13px 30px; text-decoration:none; border-radius:4px; font-size:13px; letter-spacing:1px; text-transform:uppercase; border:1px solid rgba(255,255,255,0.25); transition:all 0.3s; font-family:var(--font-ui-tamil); }
        .bk-btn-outline:hover { border-color:var(--gold); color:var(--gold); }

        .bk-hero-stats { display:flex; gap:36px; margin-top:32px; padding-top:28px; border-top:1px solid rgba(255,255,255,0.08); }
        .bk-stat-num { font-family:var(--font-heading); font-size:24px; font-weight:800; color:var(--gold); }
        .bk-stat-label { font-family:var(--font-ui-tamil); font-size:11px; color:rgba(255,255,255,0.4); letter-spacing:1px; margin-top:2px; }

        .bk-hero-right { position:relative; display:flex; align-items:center; justify-content:center; padding:40px 80px 40px 40px; }
        .bk-hero-books { position:relative; width:280px; height:340px; }
        .bk-hero-book { position:absolute; border-radius:4px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.6); }
        .bk-hero-book:nth-child(1) { width:160px; height:220px; top:50%; left:50%; transform:translate(-50%,-50%) rotate(-3deg); z-index:3; }
        .bk-hero-book:nth-child(2) { width:140px; height:200px; top:25%; left:8%; transform:rotate(8deg); z-index:2; opacity:0.8; }
        .bk-hero-book:nth-child(3) { width:135px; height:190px; bottom:10%; right:4%; transform:rotate(-6deg); z-index:2; opacity:0.75; }
        .bk-hero-book img { width:100%; height:100%; object-fit:cover; }
        .bk-hero-book-placeholder { width:100%; height:100%; background:linear-gradient(135deg,var(--dark3),#3a3020); display:flex; align-items:center; justify-content:center; font-size:40px; }

        /* ── SECTIONS ── */
																						
        .bk-section {   background-color:#faf8fd; }
		.bk-width-section {
			max-width : 1200px;
			margin: auto;
			padding-bottom :20px ;
					 }	 
        .bk-section-alt {  }
        .bk-section-dark { background:var(--dark2); }
        .bk-section-header { display:flex; align-items:flex-end; justify-content:space-between; padding:20px 0px;  }
        .bk-section-tag { font-family:var(--font-heading); font-size:10px; letter-spacing:4px; text-transform:uppercase; color:var(--gold); margin-bottom:10px; display:flex; align-items:center; gap:10px; }
        .bk-section-tag::before,.bk-section-tag::after { content:''; display:block; width:30px; height:1px; background:var(--gold); opacity:0.5; }
        .bk-section-title { font-family:var(--font-tamil); font-size:34px; font-weight:700; color:var(--text); line-height:1.3; }
        .bk-section-dark .bk-section-title { color:white; }
        .bk-section-link { display:inline-flex; align-items:center; gap:8px; color:var(--gold); text-decoration:none; font-size:13px; letter-spacing:1px; border-bottom:1px solid rgba(201,168,76,0.4); padding-bottom:4px; transition:all 0.2s; font-family:var(--font-ui-tamil); }
        .bk-section-link:hover { gap:14px; }

        /* ── BOOK CARDS ── */
        .bk-books-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; }
        .bk-book-card { background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08); transition:transform 0.3s,box-shadow 0.3s; text-decoration:none; color:inherit; display:flex; flex-direction:column; border:1px solid rgba(0,0,0,0.06); }
        .bk-book-card:hover { transform:translateY(-8px); box-shadow:0 20px 50px rgba(0,0,0,0.14); }
        .bk-book-img { position:relative; height:240px; overflow:hidden; background:#f5f0e8; flex-shrink:0; }
        .bk-book-img img { width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.5s; }
        .bk-book-card:hover .bk-book-img img { transform:scale(1.05); }
        .bk-book-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#f0e8d8,#e4d9c4); font-size:60px; color:#c9a84c; }
        .bk-book-badge { position:absolute; top:10px; left:10px; background:var(--saffron); color:white; font-size:9px; font-weight:800; padding:4px 10px; border-radius:3px; letter-spacing:1px; text-transform:uppercase; font-family:var(--font-ui-tamil); }
        .bk-book-badge-new { background:var(--rust); }
        .bk-book-badge-sale { background:#2a7a3b; }
        .bk-book-body { padding:16px; flex:1; display:flex; flex-direction:column; }
        .bk-book-cat { font-family:var(--font-ui-tamil); font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--saffron); font-weight:700; margin-bottom:7px; }
        .bk-book-title { font-family:var(--font-tamil); font-size:15px; font-weight:600; color:var(--text); line-height:1.5; margin-bottom:8px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .bk-book-author { font-family:var(--font-ui-tamil); font-size:12px; color:var(--muted); margin-bottom:12px; flex:1; }
        .bk-book-price { font-family:var(--font-heading); font-size:17px; font-weight:700; color:var(--rust); }
        .bk-book-footer { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:auto; }

        /* ── ADD TO CART BUTTON ── */
        .bk-cart-btn {
            display:inline-flex; align-items:center; gap:5px;
            background: linear-gradient(135deg, var(--lav-950) 0%, var(--lav-800) 45%, var(--lav-700) 100%);
            color:#ffffff;
            padding:8px 14px; border-radius:6px;
            font-size:11px; font-weight:700;
            text-decoration:none; letter-spacing:0.5px;
            text-transform:uppercase;
            transition:all 0.2s; white-space:nowrap; flex-shrink:0;
            font-family:var(--font-ui-tamil);
            border:none; cursor:pointer;
        }
        .bk-cart-btn:hover { background: linear-gradient(135deg,#3b0764,#4c1d95); transform:translateY(-1px); color:#fff; }

        /* Added state */
        .bk-cart-btn.added {
            background: linear-gradient(135deg,#14532d,#15803d) !important;
            pointer-events:none;
        }
        .bk-cart-btn.loading {
            opacity:0.7; pointer-events:none;
        }

        /* ── QUOTE ── */
        .bk-quote { background:linear-gradient(135deg,var(--lav-950) 0%,var(--lav-800) 45%,var(--lav-700) 100%); padding:70px 80px; text-align:center; position:relative; overflow:hidden; }
        .bk-quote::before { content:'❝'; position:absolute; top:-20px; left:50%; transform:translateX(-50%); font-size:200px; color:rgba(201,168,76,0.05); font-family:Georgia,serif; line-height:1; }
        .bk-quote-text { font-family:var(--font-tamil); font-size:26px; color:rgba(255,255,255,0.85); line-height:1.7; max-width:800px; margin:0 auto 18px; }
        .bk-quote-attr { font-family:var(--font-heading); font-size:11px; color:var(--gold); letter-spacing:4px; text-transform:uppercase; }

        /* ── CATEGORIES ── */
        .bk-cats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
        .bk-cat-card { background:var(--dark3); border:1px solid rgba(201,168,76,0.12); border-radius:8px; padding:30px 25px; text-decoration:none; display:flex; align-items:center; gap:18px; transition:all 0.3s; }
        .bk-cat-card:hover { border-color:var(--gold); background:rgba(201,168,76,0.06); transform:translateX(6px); }
        .bk-cat-icon { font-size:36px; flex-shrink:0; }
        .bk-cat-name { font-family:var(--font-tamil); font-size:16px; color:white; margin-bottom:4px; }
        .bk-cat-count { font-family:var(--font-ui-tamil); font-size:11px; color:rgba(255,255,255,0.35); letter-spacing:1px; }
        .bk-cat-arrow { margin-left:auto; color:var(--gold); opacity:0; transition:opacity 0.2s,transform 0.2s; font-size:18px; }
        .bk-cat-card:hover .bk-cat-arrow { opacity:1; transform:translateX(4px); }

        /* ── MAGAZINE STRIP ── */
        .bk-mag-strip { background:linear-gradient(135deg,var(--rust),#6b2810); padding:60px 80px; display:flex; align-items:center; justify-content:space-between; gap:40px; }
        .bk-mag-tag { font-family:var(--font-heading); font-size:10px; letter-spacing:4px; color:rgba(255,255,255,0.5); text-transform:uppercase; margin-bottom:12px; }
        .bk-mag-title { font-family:var(--font-tamil); font-size:34px; color:white; line-height:1.3; margin-bottom:15px; }
        .bk-mag-desc { font-family:var(--font-tamil); font-size:15px; color:rgba(255,255,255,0.65); line-height:1.7; max-width:500px; }
        .bk-mag-right { display:flex; align-items:center; gap:30px; flex-shrink:0; }
        .bk-mag-price-box { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); border-radius:8px; padding:20px 28px; text-align:center; }
        .bk-mag-price-label { font-family:var(--font-ui-tamil); font-size:11px; color:rgba(255,255,255,0.5); letter-spacing:2px; margin-bottom:6px; }
        .bk-mag-price-num { font-family:var(--font-heading); font-size:36px; font-weight:800; color:var(--gold2); }
        .bk-mag-price-period { font-family:var(--font-ui-tamil); font-size:12px; color:rgba(255,255,255,0.4); }
        .bk-btn-white { display:inline-flex; align-items:center; gap:10px; background:white; color:var(--rust); padding:15px 35px; text-decoration:none; border-radius:4px; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; transition:all 0.3s; font-family:var(--font-ui-tamil); }
        .bk-btn-white:hover { background:var(--gold2); transform:translateY(-2px); }

        /* ── TESTIMONIALS ── */
        .bk-testimonials { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .bk-testi { background:white; border-radius:8px; padding:30px; border:1px solid rgba(0,0,0,0.05); }
        .bk-testi::before { content:'❝'; font-size:48px; color:var(--gold); opacity:0.3; line-height:1; display:block; margin-bottom:10px; font-family:Georgia; }
        .bk-testi-text { font-family:var(--font-tamil); font-size:14px; line-height:1.8; color:var(--muted); margin-bottom:20px; font-style:italic; }
        .bk-testi-name { font-family:var(--font-ui-tamil); font-size:13px; font-weight:700; color:var(--text); }
        .bk-testi-stars { color:var(--gold); font-size:12px; margin-top:4px; }

        /* ── FOOTER ── */
        .bk-footer { background:#1a1a2e; border-top:1px solid rgba(201,168,76,0.15); padding:70px 80px 30px; }
        .bk-footer-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:50px; margin-bottom:50px; }
        .bk-footer-brand-name { font-family:var(--font-heading); font-size:22px; font-weight:800; color:var(--gold); letter-spacing:3px; margin-bottom:8px; }
        .bk-footer-brand-desc { font-family:var(--font-tamil); font-size:13px; color:rgba(255,255,255,0.4); line-height:1.8; margin-bottom:25px; }
        .bk-footer-social { display:flex; gap:12px; }
        .bk-footer-social a { width:38px; height:38px; background:rgba(201,168,76,0.1); border:1px solid rgba(201,168,76,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--gold); text-decoration:none; font-size:14px; transition:all 0.2s; }
        .bk-footer-social a:hover { background:var(--gold); color:var(--dark); }
        .bk-footer-col-title { font-family:var(--font-heading); font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--gold); margin-bottom:20px; }
        .bk-footer-links { list-style:none; }
        .bk-footer-links li { margin-bottom:10px; }
        .bk-footer-links a { font-family:var(--font-ui-tamil); color:rgba(255,255,255,0.45); text-decoration:none; font-size:13px; transition:color 0.2s; }
        .bk-footer-links a:hover { color:var(--gold); }
        .bk-footer-bottom { border-top:1px solid rgba(255,255,255,0.06); padding-top:25px; display:flex; justify-content:space-between; align-items:center; }
        .bk-footer-copy { font-family:var(--font-ui-tamil); font-size:12px; color:rgba(255,255,255,0.25); }
        .bk-footer-payments { display:flex; gap:8px; align-items:center; }
        .bk-pay-badge { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.1); border-radius:4px; padding:4px 10px; font-size:10px; color:rgba(255,255,255,0.4); letter-spacing:1px; font-family:var(--font-ui-tamil); }

        /* ── RESPONSIVE ── */
        @media(max-width:1100px){ .bk-books-grid{ grid-template-columns:repeat(3,1fr); } }
        @media(max-width:1024px) {
            .bk-hero { grid-template-columns:1fr; min-height:auto; }
            .bk-hero-right { display:none; }
            .bk-hero-left { padding:50px 30px; }
            .bk-hero-title { font-size:36px; }
            .bk-cats-grid { grid-template-columns:repeat(2,1fr); }
            .bk-section { padding:55px 30px; }
            .bk-topbar { padding:0 25px; }
            .bk-mag-strip { flex-direction:column; padding:50px 30px; }
            .bk-footer-grid { grid-template-columns:1fr 1fr; }
            .bk-footer { padding:50px 30px 25px; }
            .bk-testimonials { grid-template-columns:1fr; }
        }
        @media(max-width:768px) {
            .bk-nav { display:none; }
            .bk-hamburger { display:flex; }
            .bk-books-grid{ grid-template-columns:repeat(2,1fr); }
            .bk-hero-title { font-size:30px; }
        }
        @media(max-width:600px) {
            .bk-cats-grid { grid-template-columns:1fr; }
            .bk-hero-stats { gap:20px; }
        }
        @media(max-width:480px) {
            .bk-books-grid{ grid-template-columns:1fr; }
            .bk-hero-title { font-size:26px; }
        }
    </style>
</head>
<body>
<?php wp_body_open(); ?>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="bk-hero">
	<div class="bk-width">
    <div class="bk-hero-mandala">☸</div>
    <div class="bk-hero-left">
        <div class="bk-hero-tag">☸ புத்த மத நூல்கள்</div>
        <div class="bk-hero-subtitle">BUDDHIST WISDOM IN TAMIL</div>
        <h1 class="bk-hero-title"><span>ஞானத்தின்</span><br>வழியில் நடப்போம்</h1>
        <p class="bk-hero-desc">புத்தரின் போதனைகள், தியான நூல்கள், ஆன்மீக வழிகாட்டிகள் — அனைத்தும் உங்கள் தாய்மொழியில். உள்ளத்தை அமைதிப்படுத்தும் சிறந்த நூல்களை கண்டறியுங்கள்.</p>
        <div class="bk-hero-btns">
            <a href="<?php echo esc_url($shop_url); ?>" class="bk-btn-primary">☸ நூல்களை காண்க</a>
        </div>
        <div class="bk-hero-stats">
            <?php
            $book_count   = wp_count_posts('product')->publish;
            $order_count  = wp_count_posts('shop_order');
            $total_orders = isset($order_count->{'wc-completed'}) ? $order_count->{'wc-completed'} : '500';
            ?>
            <div><div class="bk-stat-num"><?php echo $book_count; ?>+</div><div class="bk-stat-label">நூல்கள்</div></div>
            <div><div class="bk-stat-num"><?php echo $total_orders; ?>+</div><div class="bk-stat-label">வாடிக்கையாளர்கள்</div></div>
            <div><div class="bk-stat-num">24/7</div><div class="bk-stat-label">ஆதரவு</div></div>
        </div>
    </div>
    <div class="bk-hero-right">
        <div class="bk-hero-books">
            <?php
            $hero_books = new WP_Query(array('post_type'=>'product','posts_per_page'=>3,'orderby'=>'date','order'=>'DESC'));
            if ($hero_books->have_posts()):
                while ($hero_books->have_posts()): $hero_books->the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            ?>
            <div class="bk-hero-book">
                <?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"><?php else: ?><div class="bk-hero-book-placeholder">&#128214;</div><?php endif; ?>
            </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </div>
				</div>
</section>

<!-- ═══════════════ NEW BOOKS ═══════════════ -->
<section class="bk-section">
	<div class="bk-width-section">
    <div class="bk-section-header">
        <div>
           
            <h2 class="bk-section-title">புதிய நூல்கள்</h2>
        </div>
        <a href="<?php echo esc_url($shop_url); ?>" class="bk-section-link">அனைத்தும் காண்க →</a>
    </div>
    <div class="bk-books-grid">
        <?php
        $displayed = 0;
        $all_books = new WP_Query(array(
            'post_type'      => 'product',
            'posts_per_page' => 30,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(array(
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS',
            )),
        ));
        if ($all_books->have_posts()):
            while ($all_books->have_posts() && $displayed < 8):
                $all_books->the_post();
                global $product;
                $product  = wc_get_product(get_the_ID());
                $img      = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                if (!$img) continue;
                $displayed++;
                $price       = $product ? $product->get_price_html() : '';
                $cats        = get_the_terms(get_the_ID(), 'product_cat');
                $cat_name    = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : '';
                $is_variable = $product && $product->is_type('variable');
        ?>
        <div class="bk-book-card">
            <a href="<?php echo esc_url(get_the_permalink()); ?>" style="text-decoration:none;color:inherit;">
                <div class="bk-book-img">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <span class="bk-book-badge">புதியது</span>
                </div>
            </a>
            <div class="bk-book-body">
                <?php if ($cat_name): ?>
                <div class="bk-book-cat"><?php echo esc_html($cat_name); ?></div>
                <?php endif; ?>
                <a href="<?php echo esc_url(get_the_permalink()); ?>" style="text-decoration:none;">
                    <h3 class="bk-book-title"><?php the_title(); ?></h3>
                </a>
                <div class="bk-book-author"><?php echo esc_html(get_post_meta(get_the_ID(), '_author', true) ?: 'போதி முரசு வெளியீடு'); ?></div>
                <div class="bk-book-footer">
                    <div class="bk-book-price"><?php echo $price; ?></div>
                    <?php if ($is_variable): ?>
                    <a href="<?php echo esc_url(get_the_permalink()); ?>" class="bk-cart-btn">&#128722; தேர்ந்தெடு</a>
                    <?php else: ?>
                    <button class="bk-cart-btn bk-ajax-cart"
                            data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
                        &#128722; Add to Cart
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
			</div>
</section>

<!-- ═══════════════ QUOTE ═══════════════ -->
<div class="bk-quote">
    <p class="bk-quote-text">"உங்கள் மனமே உங்கள் எல்லாவற்றையும் உருவாக்குகிறது. தூய்மையான மனதுடன் பேசுங்கள் அல்லது செயல்படுங்கள் — மகிழ்ச்சி நிழலைப் போல உங்களை பின்தொடரும்."</p>
    <div class="bk-quote-attr">— புத்தர் (Dhammapada)</div>
</div>

<!-- ═══════════════ BESTSELLERS ═══════════════ -->
<section class="bk-section bk-section-alt">
			<div class="bk-width-section">
    <div class="bk-section-header">
        <div>
            <h2 class="bk-section-title">சிறந்த நூல்கள்</h2>
        </div>
        <a href="<?php echo esc_url($shop_url); ?>" class="bk-section-link">அனைத்தும் காண்க →</a>
    </div>
    <div class="bk-books-grid">
        <?php
        $displayed   = 0;
        $bestsellers = new WP_Query(array(
            'post_type'      => 'product',
            'posts_per_page' => 30,
            'meta_key'       => 'total_sales',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => array(array(
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS',
            )),
        ));
        if ($bestsellers->have_posts()):
            while ($bestsellers->have_posts() && $displayed < 8):
                $bestsellers->the_post();
                global $product;
                $product     = wc_get_product(get_the_ID());
                $img         = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                if (!$img) continue;
                $displayed++;
                $price       = $product ? $product->get_price_html() : '';
                $cats        = get_the_terms(get_the_ID(), 'product_cat');
                $cat_name    = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : '';
                $is_variable = $product && $product->is_type('variable');
        ?>
        <div class="bk-book-card">
            <a href="<?php echo esc_url(get_the_permalink()); ?>" style="text-decoration:none;color:inherit;">
                <div class="bk-book-img">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <span class="bk-book-badge bk-book-badge-sale">சிறந்தது</span>
                </div>
            </a>
            <div class="bk-book-body">
                <?php if ($cat_name): ?>
                <div class="bk-book-cat"><?php echo esc_html($cat_name); ?></div>
                <?php endif; ?>
                <a href="<?php echo esc_url(get_the_permalink()); ?>" style="text-decoration:none;">
                    <h3 class="bk-book-title"><?php the_title(); ?></h3>
                </a>
                <div class="bk-book-author"><?php echo esc_html(get_post_meta(get_the_ID(), '_author', true) ?: 'போதி முரசு வெளியீடு'); ?></div>
                <div class="bk-book-footer">
                    <div class="bk-book-price"><?php echo $price; ?></div>
                    <?php if ($is_variable): ?>
                    <a href="<?php echo esc_url(get_the_permalink()); ?>" class="bk-cart-btn">&#128722; தேர்ந்தெடு</a>
                    <?php else: ?>
                    <button class="bk-cart-btn bk-ajax-cart"
                            data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
                        &#128722; Add to Cart
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
			</div>
</section>

<!-- ═══════════════ AJAX ADD TO CART SCRIPT ═══════════════ -->
<script>
(function () {
    // WooCommerce AJAX endpoint and nonce provided by wp_localize_script (wc_add_to_cart_params)
    // Fallback to standard WooCommerce AJAX URL
    var ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined')
        ? wc_add_to_cart_params.ajax_url
        : '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.bk-ajax-cart');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        var productId = btn.getAttribute('data-product-id');
        if (!productId) return;

        // Show loading state
        btn.classList.add('loading');
        btn.innerHTML = '⏳ Adding...';

        var formData = new FormData();
        formData.append('action', 'woocommerce_ajax_add_to_cart');  // WooCommerce standard action
        formData.append('product_id', productId);
        formData.append('quantity', 1);

        // Use WooCommerce's standard ?add-to-cart= approach via fetch, suppressing redirect
        fetch('<?php echo esc_js(home_url('/')); ?>?add-to-cart=' + productId + '&quantity=1', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            redirect: 'manual'   // ← prevents following any redirect
        })
        .then(function () {
            // Success — show Added state regardless of response body
            btn.classList.remove('loading');
            btn.classList.add('added');
            btn.innerHTML = '✓ Added';

            // Update cart count in header if present
            if (typeof wc_add_to_cart_params !== 'undefined') {
                jQuery(document.body).trigger('wc_fragment_refresh');
            }

            // Reset button text after 2.5 seconds so user can add again
            setTimeout(function () {
                btn.classList.remove('added');
                btn.innerHTML = '&#128722; Add to Cart';
                btn.style.pointerEvents = '';
            }, 2500);
        })
        .catch(function () {
            btn.classList.remove('loading');
            btn.innerHTML = '&#128722; Add to Cart';
        });
    });
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
    <?php
    $html = ob_get_clean();
    echo $html;
    exit;
}
add_action('template_redirect', 'buddhist_bookstore_homepage');