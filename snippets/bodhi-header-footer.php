<?php
// =============================================
// GLOBAL SITE HEADER
// ============================================
function bodhi_global_header() {
    $logo_url    = 'https://bodhimurasu.org/wp-content/uploads/logo.png';
    $cart_url    = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/';
    $account_url = function_exists('wc_get_account_endpoint_url') ? wc_get_page_permalink('myaccount') : '/my-account/';
    $cart_count  = function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;
    $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));

    $nav_items = array(
		
         'இதழ்கள்'     => home_url('/'),
        'புத்தகங்கள்'  => home_url('/books'),
        'தொடர்பு'      => home_url('/contact-us/'),
    );

    ob_start();
    ?>
    <style>
		:root {
            --gold:    #c9a84c;
            --gold2:   #f0d080;
            --dark:    #0f0e0b;
            --dark2:   #1a1810;
            --dark3:   #252318;
            --cream:   #f5f0e8;
            --cream2:  #ede5d4;
            --rust:    #8b3a1a;
            --saffron: #e8890c;
            --text:    #2a2318;
            --muted:   #7a6f5e;
			--white:   #FFFFFF;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
	
	 body {
            font-family: 'Tiro Tamil', Georgia, serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
		 	
        }
		
        .bodhi-header-wrap {
           background: #3F2D56;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
			 display: flex;
            align-items: center;
            justify-content: space-between;
			
        }
		.bothi-wrap{
			max-width: 1200px;
			width: 100%;
			margin: 0 auto;	
		}
        .bodhi-header-inner {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 50px;
        }
        .bodhi-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .bodhi-logo img {
            height: 45px;
            width: auto;
            object-fit: contain;
        }
        .bodhi-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
			
        }
       
        .bodhi-logo-text span:first-child {
            font-size: 1.1rem;
            color: var(--white);
            letter-spacing: 1px;
			font-weight: 700;
			
        }
        .bodhi-nav {
            display: flex;
            align-items: center;
            gap: 35px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .bodhi-nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: color 0.2s;
            position: relative;
            padding-bottom: 4px;
        }
        .bodhi-nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #FFFFFF;
            transition: width 0.3s;
        }
        .bodhi-nav a:hover,
        .bodhi-nav a.active {
            color: #FFFFFF;
        }
        .bodhi-nav a:hover::after,
        .bodhi-nav a.active::after {
            width: 100%;
        }
        .bodhi-header-icons {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .bodhi-header-icons a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 20px;
            transition: color 0.2s;
            position: relative;
            display: flex;
            align-items: center;
        }
        .bodhi-header-icons a:hover { color: #c9a84c; }
        .bodhi-cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #c9a84c;
            color: #0d0d0d;
            font-size: 10px;
            font-weight: 800;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bodhi-hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 5px;
        }
        .bodhi-hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: white;
            transition: all 0.3s;
        }
        .bodhi-mobile-nav {
            display: none;
            background: #111;
            padding: 20px 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .bodhi-mobile-nav a {
            display: block;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .bodhi-mobile-nav a:hover { color: #c9a84c; }
        @media(max-width:768px) {
            .bodhi-nav { display: none; }
            .bodhi-hamburger { display: flex; }
        }
        @media(max-width:480px) {
            .bodhi-header-inner { padding: 0 15px; }
        }
    </style>
		

    <div class="bodhi-header-wrap">
		<div class="bothi-wrap">
        <div class="bodhi-header-inner">

            <!-- LOGO -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="bodhi-logo">

                <div class="bodhi-logo-text">

                    <span>போதி முரசு</span>
                </div>
            </a>

            <!-- DESKTOP NAV -->
            <nav>
                <ul class="bodhi-nav">
                    <?php foreach ($nav_items as $label => $url) :
                        $is_active = (rtrim($_SERVER['REQUEST_URI'], '/') === rtrim(parse_url($url, PHP_URL_PATH), '/'));
                        $active_class = $is_active ? ' active' : '';
                    ?>
                    <li><a href="<?php echo esc_url($url); ?>" class="<?php echo $active_class; ?>"><?php echo esc_html($label); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- ICONS -->
            <div class="bodhi-header-icons">
                <!-- Account -->
                <a href="<?php echo esc_url($account_url); ?>" title="My Account">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <!-- Cart -->
                <a href="<?php echo esc_url($cart_url); ?>" title="Cart">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <?php if ($cart_count > 0) : ?>
                    <span class="bodhi-cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <!-- Hamburger -->
                <div class="bodhi-hamburger" onclick="document.getElementById('bodhiMobileNav').style.display = document.getElementById('bodhiMobileNav').style.display === 'block' ? 'none' : 'block'">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        <!-- MOBILE NAV -->
        <div class="bodhi-mobile-nav" id="bodhiMobileNav">
            <?php foreach ($nav_items as $label => $url) : ?>
            <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
				</div>
    <?php
    return ob_get_clean();
}
add_shortcode('bodhi_header', 'bodhi_global_header');


// =============================================
// GLOBAL SITE FOOTER
// =============================================
function bodhi_global_footer() {
    $logo_url = 'https://bodhimurasu.org/wp-content/uploads/logo.png';

    $footer_links = array(
        'இதழ்கள்' => array(
            'நடப்பு இதழ்'    => home_url('/'),
            'முந்தைய இதழ்கள்'       => home_url('/'),
            'இலவச கட்டுரைகள்'   => home_url('/'),
            'சந்தா சேருங்கள்'   => home_url('/'),
        ),
        'புத்தகங்கள்' => array(
            'அனைத்து புத்தகங்கள்'        => home_url('/'),
            'புதிய வெளியீடுகள்'         => home_url('/'),
            'மேத்தா பதிப்பகம்'        => home_url('/'),
           
        ),
        'பற்றி' => array(
            'எங்களைப் பற்றி'       => home_url('/'),
            'தொடர்பு' => home_url('/'),
            'தனியுரிமைக் கொள்கை'            => home_url('/'),
            'விதிமுறைகள்'     => home_url('/'),
            'இதழ் சந்தா'      => home_url('/'),
        ),
    );

    ob_start();
    ?>
    <style>
        .bodhi-footer-wrap {
            background: #2d1b4e;
            color: rgba(255,255,255,0.75);
            width: 100%;
           
        }
	.bothi-wrap{
			max-width: 1200px;
			width: 100%;
			margin: 0 auto;	
		}
        .bodhi-footer-top {
            margin: 0 auto;
            padding: 60px 30px 40px;
            display: grid;
            grid-template-columns: 1.5fr repeat(3, 1fr);
            gap: 40px;
        }
        .bodhi-footer-brand img {
            height: 50px;
            width: auto;
            margin-bottom: 15px;
            display: block;
        }
        .bodhi-footer-brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
		
        .bodhi-footer-brand p {
            font-size: 0.85rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }
        .bodhi-footer-social {
            display: flex;
            gap: 10px;
        }
        .bodhi-footer-social a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .bodhi-footer-social a:hover {
            background: #ffffffb3;
            color: #ffffffb3;
            border-color: #ffffffb3;
        }
        .bodhi-footer-col h4 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .bodhi-footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .bodhi-footer-col ul li {
            margin-bottom: 10px;
        }
        .bodhi-footer-col ul li a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .bodhi-footer-col ul li a:hover { color: #FFFFFF; }
        .bodhi-footer-col ul li a::before {
            color: #FFFFFF;
            font-size: 16px;
        }
        .bodhi-footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        .bodhi-footer-copy {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }
        .bodhi-payment-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .bodhi-payment-icons span {
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.15);
        }
        @media(max-width:900px) {
            .bodhi-footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media(max-width:560px) {
            .bodhi-footer-top { grid-template-columns: 1fr; padding: 40px 20px 30px; }
            .bodhi-footer-bottom { flex-direction: column; text-align: center; padding: 20px; }
        }
    </style>

    <div class="bodhi-footer-wrap">
		<div class="bothi-wrap">
        <div class="bodhi-footer-top">

            <!-- BRAND -->
            <div class="bodhi-footer-brand">
                <div class="bodhi-footer-brand-name">போதி முரசு</div>
                <p>புத்த தர்மம், தியானம், வாழ்க்கை நெறிகள் பற்றிய ஆழமான கட்டுரைகளை தமிழில் படியுங்கள்.</p>
				 <p>வெளியீடு: மேத்தா பதிப்பகம்
தமிழ்நாடு பௌத்த சங்க அறக்கட்டளை</p>
                <div class="bodhi-footer-social">
                    <a href="#" title="Facebook">f</a>
                    <a href="#" title="Instagram">in</a>
					 <a href="#" title="Twitter">𝕏</a>
                </div>
            </div>

            <!-- FOOTER COLUMNS -->
            <?php foreach ($footer_links as $col_title => $links) : ?>
            <div class="bodhi-footer-col">
                <h4><?php echo esc_html($col_title); ?></h4>
                <ul>
                    <?php foreach ($links as $label => $url) : ?>
                    <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
			
        
		</div>
        <!-- BOTTOM BAR -->
        <div class="bodhi-footer-bottom">
            <p class="bodhi-footer-copy">
                &copy; <?php echo date('Y'); ?> போதி முரசு. அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை.
            </p>
            <div class="bodhi-payment-icons">
                <span>RAZORPAY</span>
                <span>UPI</span>
                <span>VISA</span>
                <span>MASTERCARD</span>
            </div>
        </div>
		</div>	
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('bodhi_footer', 'bodhi_global_footer');


// =============================================
// AUTO INJECT HEADER + FOOTER ON ALL PAGES
// (except home page which has its own)
// =============================================
function bodhi_inject_header_footer() {
    // Inject header before content on ALL pages
    add_action('wp_body_open', function() {
        echo do_shortcode('[bodhi_header]');
    }, 1);

    // Inject footer after content on ALL pages
    add_action('wp_footer', function() {
        echo do_shortcode('[bodhi_footer]');
    }, 1);

    // Hide BookStore Lite default header/footer on ALL pages
    add_action('wp_head', function() {
        echo '<style>
            /* Hide default BookStore Lite header and footer */
            .site-header,
            header.site-header,
            #masthead,
            .main-navigation,
            header#header,
            .header-main { display: none !important; }

            .site-footer,
            footer.site-footer,
            #colophon,
            footer#footer { display: none !important; }

            /* Remove top margin caused by hidden header */
            .site-content,
            #content,
            .content-area {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
        </style>';
    });
}
add_action('init', 'bodhi_inject_header_footer');