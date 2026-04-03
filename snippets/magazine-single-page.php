<?php

// MAGAZINE SINGLE PAGE - WOOCOMMERCE SUBSCRIPTION CHECK
function magazine_single_redirect() {
    if (is_singular('magazine_issue')) {
        $post_id    = get_the_ID();
        $title      = get_the_title();
        $thumb      = get_the_post_thumbnail_url($post_id, 'full');
        $month_year = get_field('issue_month_year');
        $category   = get_field('category_label');
        $author     = get_field('author_name');
        $desc       = get_field('short_description');
        $content    = get_the_content();
        $content    = apply_filters('the_content', $content);
        $issue_num  = get_field('issue_number');

        // ACF ACCESS FIELDS
        $visibility            = get_field('magazine_visibility');
        $requires_subscription = get_field('requires_subscription');
        if (!$visibility) { $visibility = 'everyone'; }

        // WOOCOMMERCE SUBSCRIPTION PRODUCT ID
        $subscription_product_id = 782;

        // URLs
        $checkout_url = wc_get_checkout_url() . '?add-to-cart=' . $subscription_product_id;
        $login_url    = 'https://bodhimurasu.org/my-account/';
        $free_reg_url = wp_registration_url();

        // STEP 1: Check if user is logged in
        $is_logged_in = is_user_logged_in();

        // STEP 2: Admin and editor always get full access
        $has_subscription = false;
        if (current_user_can('administrator') || current_user_can('editor')) {
            $has_subscription = true;
        }

        // STEP 3: For logged in non-admin users, check if they purchased product 782
        if (!$has_subscription && $is_logged_in) {
            $user_id = get_current_user_id();

            if (function_exists('wc_customer_bought_product')) {
                $user_info = get_userdata($user_id);
                $email     = $user_info ? $user_info->user_email : '';
                if (wc_customer_bought_product($email, $user_id, $subscription_product_id)) {
                    $has_subscription = true;
                }
            }

            if (!$has_subscription) {
                $orders = wc_get_orders(array(
                    'customer_id' => $user_id,
                    'status'      => array('wc-completed', 'wc-processing'),
                    'limit'       => -1,
                ));
                foreach ($orders as $order) {
                    foreach ($order->get_items() as $item) {
                        if ((int)$item->get_product_id() === (int)$subscription_product_id) {
                            $has_subscription = true;
                            break 2;
                        }
                    }
                }
            }
        }

        // STEP 4: Decide what to show
        $show_full    = false;
        $show_paywall = false;
        $show_login   = false;

        if ($requires_subscription) {
            if ($has_subscription) {
                $show_full = true;
            } else {
                $show_paywall = true;
            }
        } elseif ($visibility === 'login') {
            if ($is_logged_in) {
                $show_full = true;
            } else {
                $show_login = true;
            }
        } else {
            $show_full = true;
        }

        // Extract year from current issue's month_year
        $current_year = '';
        if ($month_year) {
            preg_match('/\d{4}/', $month_year, $year_match);
            $current_year = isset($year_match[0]) ? $year_match[0] : '';
        }

        // Get more issues (related)
        $more_issues_args = array(
            'post_type'      => 'magazine_issue',
            'posts_per_page' => 6,
            'post__not_in'   => array($post_id),
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ($current_year) {
            $more_issues_args['meta_query'] = array(
                array(
                    'key'     => 'issue_month_year',
                    'value'   => $current_year,
                    'compare' => 'LIKE',
                ),
            );
        }
        $more_issues = new WP_Query($more_issues_args);

        // Breadcrumb / nav
        $archive_url  = get_post_type_archive_link('magazine_issue');
        $category_url = '#';

        ob_start();
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?> - போதி முரசு</title>
    <?php wp_head(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Tamil:wght@400;600;700&family=Noto+Sans+Tamil:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── RESET ── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --font-body:     'Noto Serif Tamil', Latha, 'Nirmala UI', serif;
            --font-ui:       'Noto Sans Tamil', 'Nirmala UI', sans-serif;

            /* Purple palette */
            --purple-dark:   #2d1b4e;
            --purple-mid:    #4a2d73;
            --purple-light:  #6d28d9;
            --purple-btn:    #3b1fa8;

            --page-bg:       #ffffff;
            --card-bg:       #ffffff;          /* ← changed: white, no tint */
            --white:         #ffffff;

            /* Text */
            --color-text:    #1a1a2e;
            --color-muted:   #6b6880;
            --color-border:  #e2ddf0;
        }

        html { font-size: 16px; background: #f7f5ff !important; }

        body {
            font-family: var(--font-body);
            background: #f7f5ff !important;
            color: var(--color-text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

        /* ══════════════════════════════════
           SITE HEADER — dark purple bar
        ══════════════════════════════════ */
        .site-header {
            background: var(--purple-dark);
            position: sticky;
            top: 0;
            z-index: 200;
        }
        .site-header-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
            height: 52px;
            display: flex;
            align-items: center;
            gap: 0;
        }
        .site-logo {
            font-family: var(--font-ui);
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.2px;
            margin-right: 40px;
            flex-shrink: 0;
        }
        .site-nav {
            display: flex;
            gap: 0;
            flex: 1;
        }
        .site-nav a {
            font-family: var(--font-ui);
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,.78);
            padding: 0 18px;
            height: 52px;
            display: flex;
            align-items: center;
            border-bottom: 3px solid transparent;
            transition: color .15s, border-color .15s;
        }
        .site-nav a:hover,
        .site-nav a.active {
            color: #fff;
            border-bottom-color: #a78bfa;
        }
        .site-nav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .site-nav-right a {
            color: rgba(255,255,255,.75);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background .15s;
        }
        .site-nav-right a:hover { background: rgba(255,255,255,.12); }
        .site-nav-right svg {
            width: 18px; height: 18px;
            fill: none; stroke: currentColor;
            stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ══════════════════════════════════
           BREADCRUMBS
        ══════════════════════════════════ */
        .breadcrumb-bar {
            max-width: 1180px;
            margin: 0 auto;
            padding: 16px 24px 0;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            font-family: var(--font-ui);
            font-size: 13px;
            color: var(--color-muted);
            flex-wrap: wrap;
        }
        .breadcrumb a { color: var(--color-muted); transition: color .15s; }
        .breadcrumb a:hover { color: var(--color-text); }
        .breadcrumb-sep { margin: 0 6px; opacity: .5; }
        .breadcrumb-current {
            color: var(--color-muted);
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ══════════════════════════════════
           PAGE LAYOUT
        ══════════════════════════════════ */
        .page-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 20px 24px 60px;
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }

        /* ── MAIN ARTICLE ── */
        .article-main {
            flex: 1;
            min-width: 0;
        }

        /* Hero image */
        .article-hero {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            border-radius: 6px;
            display: block;
            margin-bottom: 20px;
        }

        /* Category */
        .article-category {
            font-family: var(--font-ui);
            font-size: 12px;
            font-weight: 600;
            color: var(--color-muted);
            letter-spacing: 0.6px;
            margin-bottom: 8px;
            display: block;
        }

        /* Title */
        .article-title {
            font-family: var(--font-body);
            font-size: 30px;
            font-weight: 700;
            color: var(--color-text);
            line-height: 1.45;
            margin-bottom: 14px;
        }

        /* Meta row */
        .article-meta {
            display: flex;
            align-items: center;
            font-family: var(--font-ui);
            font-size: 13px;
            color: var(--color-muted);
            padding: 10px 0;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 4px;
        }
        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .meta-sep { margin: 0 8px; color: #bbb; }
        .article-meta-item svg {
            width: 13px; height: 13px;
            fill: none; stroke: currentColor;
            stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
            flex-shrink: 0;
        }

        /* Lead */
        .article-lead {
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.9;
            color: #555;
            margin-bottom: 24px;
        }

        /* Content */
        .article-content {
            font-family: var(--font-body);
            font-size: 16px;
            line-height: 2;
            color: #333;
        }
        .article-content p { margin-bottom: 18px; }
        .article-content h2 { font-size: 22px; font-weight: 700; color: var(--color-text); margin: 32px 0 12px; }
        .article-content h3 { font-size: 18px; font-weight: 700; color: var(--color-text); margin: 24px 0 10px; }
        .article-content img { width: 100%; border-radius: 4px; margin: 22px 0; }
        .article-content blockquote { border-left: 3px solid #c4b5fd; padding: 12px 18px; margin: 22px 0; font-style: italic; color: #555; }
        .article-content ul, .article-content ol { padding-left: 22px; margin-bottom: 18px; }
        .article-content li { margin-bottom: 7px; line-height: 1.8; }

        /* Preview fade */
        .preview-wrap { position: relative; overflow: hidden; max-height: 200px; }
        .preview-fade {
            position: absolute; bottom: 0; left: 0; right: 0; height: 120px;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, var(--page-bg) 100%);
        }

        /* ── WALLS ── */
        .wall {
            background: var(--white);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 36px 28px;
            text-align: center;
            margin-top: 4px;
        }
        .wall-icon { font-size: 36px; margin-bottom: 12px; }
        .wall-title { font-family: var(--font-body); font-size: 20px; font-weight: 700; color: var(--color-text); margin-bottom: 10px; }
        .wall-desc { font-family: var(--font-ui); font-size: 14px; color: var(--color-muted); line-height: 1.8; margin-bottom: 24px; max-width: 420px; margin-left: auto; margin-right: auto; }

        .btn-primary {
            display: inline-block;
            background: var(--purple-dark);
            color: #fff;
            padding: 12px 28px;
            border-radius: 4px;
            font-family: var(--font-ui);
            font-size: 14px;
            font-weight: 600;
            transition: opacity .2s;
            margin-bottom: 12px;
        }
        .btn-primary:hover { opacity: .85; }
        .btn-secondary {
            display: inline-block;
            border: 1px solid var(--color-border);
            color: var(--color-text);
            padding: 11px 26px;
            border-radius: 4px;
            font-family: var(--font-ui);
            font-size: 14px;
            font-weight: 500;
            transition: background .15s;
            margin-left: 8px;
        }
        .btn-secondary:hover { background: #f0edf8; }

        .price-box { border: 1px solid var(--color-border); border-radius: 8px; padding: 22px 24px; max-width: 300px; margin: 0 auto 22px; background: #fff; }
        .price-label { font-family: var(--font-ui); font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--color-muted); margin-bottom: 8px; }
        .price-amount { font-family: var(--font-body); font-size: 40px; font-weight: 700; color: var(--color-text); line-height: 1; margin-bottom: 4px; }
        .price-period { font-family: var(--font-ui); font-size: 13px; color: var(--color-muted); margin-bottom: 18px; }
        .features-list { list-style: none; padding: 0; text-align: left; }
        .features-list li { font-family: var(--font-ui); font-size: 13px; color: var(--color-text); padding: 8px 0; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 8px; }
        .features-list li:last-child { border-bottom: none; }
        .features-list .ck { color: var(--purple-light); font-weight: 700; }

        .wall-already { font-family: var(--font-ui); font-size: 13px; color: var(--color-muted); margin-top: 12px; }
        .wall-already a { color: var(--purple-btn); text-decoration: underline; text-underline-offset: 2px; }

        .user-notice { background: #f0edf8; border: 1px solid var(--color-border); border-radius: 4px; padding: 10px 14px; font-family: var(--font-ui); font-size: 13px; color: var(--color-muted); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

        .divider { border: none; border-top: 1px solid var(--color-border); margin: 32px 0; }

        /* ══════════════════════════════════
           SIDEBAR
        ══════════════════════════════════ */
        .article-sidebar {
            width: 270px;
            flex-shrink: 0;
            position: sticky;
            top: 68px;
        }

        .sidebar-section { margin-bottom: 20px; }

        /* Related articles box */
        .sidebar-box {
            background: var(--card-bg);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .sidebar-box-hdr {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--color-border);
        }
        .sidebar-heading {
            font-family: var(--font-ui);
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text);
            letter-spacing: 0.2px;
        }

        /* Related cards inside the box */
        .related-card {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px 14px;
            border-bottom: 1px solid var(--color-border);
            text-decoration: none;
            transition: background .15s;
        }
        .related-card:last-child { border-bottom: none; }
        .related-card:hover { background: rgba(109,40,217,.06); }
        .related-card-thumb {
            width: 68px;
            height: 54px;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0;
            background: #d8d4e8;
        }
        .related-card-body { flex: 1; min-width: 0; }
        .related-card-title {
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text);
            line-height: 1.5;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .related-card-meta {
            font-family: var(--font-ui);
            font-size: 11px;
            color: var(--color-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }
        .related-card-meta .dot { color: #bbb; }

        /* Subscribe box — dark purple */
        .subscribe-box {
            background: var(--purple-dark);
            border: none;
            border-radius: 8px;
            padding: 22px 18px 20px;
            text-align: center;
        }
        .subscribe-box-title {
            font-family: var(--font-ui);
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .subscribe-box-desc {
            font-family: var(--font-ui);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.8;
            margin-bottom: 16px;
        }
        .subscribe-box-btn {
            display: block;
            background: #ffffff;
            color: var(--purple-dark);
            padding: 11px 16px;
            border-radius: 4px;
            font-family: var(--font-ui);
            font-size: 13px;
            font-weight: 700;
            transition: opacity .2s;
        }
        .subscribe-box-btn:hover { opacity: .88; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .page-wrap { flex-direction: column; padding: 16px 16px 40px; gap: 24px; }
            .article-sidebar { width: 100%; position: static; }
        }
        @media (max-width: 600px) {
            .article-title { font-size: 22px; }
            .site-nav a:not(:first-child):not(:nth-child(2)) { display: none; }
        }
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ══════════════════════════════════
     SITE HEADER — dark purple
══════════════════════════════════ -->
<header class="site-header">
    <div class="site-header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">போதி முரசு</a>

        <nav class="site-nav">
            <a href="<?php echo esc_url($archive_url ? $archive_url : home_url('/')); ?>" class="active">இதழ்கள்</a>
            <a href="<?php echo esc_url(home_url('/புத்தகங்கள்')); ?>">புத்தகங்கள்</a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>">தொடர்பு</a>
        </nav>

        <div class="site-nav-right">
            <a href="<?php echo esc_url($login_url); ?>" title="உள்நுழைக">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </a>
            <a href="<?php echo esc_url($checkout_url); ?>" title="சந்தா">
                <svg viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </a>
        </div>
    </div>
</header>

<!-- ══════════════════════════════════
     BREADCRUMBS
══════════════════════════════════ -->
<nav class="breadcrumb-bar" aria-label="breadcrumb">
    <div class="breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">முகப்பு</a>
        <span class="breadcrumb-sep">›</span>

        <?php if ($month_year) : ?>
        <a href="<?php echo esc_url($archive_url ? $archive_url : '#'); ?>"><?php echo esc_html($month_year); ?></a>
        <span class="breadcrumb-sep">›</span>
        <?php endif; ?>

        <?php if ($category) : ?>
        <a href="<?php echo esc_url($category_url); ?>"><?php echo esc_html($category); ?></a>
        <span class="breadcrumb-sep">›</span>
        <?php endif; ?>

        <span class="breadcrumb-current" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></span>
    </div>
</nav>

<!-- ══════════════════════════════════
     PAGE BODY
══════════════════════════════════ -->
<div class="page-wrap">

    <!-- ════ MAIN ARTICLE ════ -->
    <main class="article-main">

        <?php if ($thumb) : ?>
        <img class="article-hero" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>">
        <?php endif; ?>

        <?php if ($category) : ?>
        <span class="article-category"><?php echo esc_html($category); ?></span>
        <?php endif; ?>

        <h1 class="article-title"><?php echo esc_html($title); ?></h1>

        <!-- Meta row -->
        <div class="article-meta">
            <?php if ($author) : ?>
            <div class="article-meta-item">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <?php echo esc_html($author); ?>
            </div>
            <?php endif; ?>

            <?php if ($month_year) : ?>
            <span class="meta-sep">|</span>
            <div class="article-meta-item">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html($month_year); ?>
            </div>
            <?php endif; ?>

            <?php if ($issue_num) : ?>
            <span class="meta-sep">|</span>
            <div class="article-meta-item">
                <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                இதழ் <?php echo esc_html($issue_num); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($desc) : ?>
        <div class="article-lead"><?php echo esc_html($desc); ?></div>
        <?php endif; ?>

        <!-- ── CONTENT / WALLS ── -->

        <?php if ($show_full) : ?>

            <div class="article-content">
                <?php echo $content; ?>
            </div>

        <?php elseif ($show_login) : ?>

            <div class="preview-wrap">
                <div class="article-content">
                    <p><?php echo esc_html(wp_trim_words(strip_tags($content), 60, '...')); ?></p>
                </div>
                <div class="preview-fade"></div>
            </div>

            <div class="wall">
                <div class="wall-icon">&#128100;</div>
                <h2 class="wall-title">இந்த இதழை படிக்க உள்நுழைக</h2>
                <p class="wall-desc">இந்த இதழ் பதிவு செய்த உறுப்பினர்களுக்கு மட்டுமே கிடைக்கும். இலவசமாக பதிவு செய்து படிக்கலாம்.</p>
                <a href="<?php echo esc_url($login_url); ?>" class="btn-primary">உள்நுழைக</a>
                <a href="<?php echo esc_url($free_reg_url); ?>" class="btn-secondary">பதிவு செய்க</a>
                <p class="wall-already">கணக்கு இல்லையா? <a href="<?php echo esc_url($free_reg_url); ?>">இலவசமாக பதிவு செய்க</a></p>
            </div>

        <?php elseif ($show_paywall) : ?>

            <div class="preview-wrap">
                <div class="article-content">
                    <p><?php echo esc_html(wp_trim_words(strip_tags($content), 60, '...')); ?></p>
                </div>
                <div class="preview-fade"></div>
            </div>

            <?php if ($is_logged_in) : ?>
            <div class="user-notice">
                <span>&#128100;</span>
                <span>நீங்கள் உள்நுழைந்துள்ளீர்கள் — ஆனால் சந்தா எடுக்கவில்லை.</span>
            </div>
            <?php endif; ?>

            <div class="wall">
                <div class="wall-icon">&#128274;</div>
                <h2 class="wall-title">சந்தா எடுத்து முழுமையாக படிக்கவும்</h2>
                <p class="wall-desc">போதி முரசு சந்தாதாரர்களுக்கு மட்டுமே இந்த இதழின் முழு உள்ளடக்கம் கிடைக்கும்.</p>

                <div class="price-box">
                    <p class="price-label">Magazine Subscription</p>
                    <p class="price-amount">&#8377;99</p>
                    <p class="price-period">மாதந்தோறும் / per month</p>
                    <ul class="features-list">
                        <li><span class="ck">&#10003;</span> அனைத்து சந்தா இதழ்களும்</li>
                        <li><span class="ck">&#10003;</span> புதிய இதழ் உடனே படிக்கலாம்</li>
                        <li><span class="ck">&#10003;</span> முழு கட்டுரைகள்</li>
                        <li><span class="ck">&#10003;</span> 24/7 எந்த நேரத்திலும்</li>
                    </ul>
                </div>

                <a href="<?php echo esc_url($checkout_url); ?>" class="btn-primary">
                    இப்போதே சந்தா எடுக்கவும் — &#8377;99/மாதம்
                </a>

                <?php if (!$is_logged_in) : ?>
                <p class="wall-already">
                    ஏற்கெனவே சந்தாதாரராக உள்ளீர்களா?
                    <a href="<?php echo esc_url($login_url); ?>">உள்நுழைக</a>
                </p>
                <?php endif; ?>
            </div>

        <?php endif; ?>

        <hr class="divider">

    </main><!-- /.article-main -->

    <!-- ════ SIDEBAR ════ -->
    <aside class="article-sidebar">

        <!-- Related articles -->
        <?php if ($more_issues->have_posts()) : ?>
        <div class="sidebar-section">
            <div class="sidebar-box">
                <div class="sidebar-box-hdr">
                    <h2 class="sidebar-heading">தொடர்புடைய கட்டுரைகள்</h2>
                </div>

                <?php while ($more_issues->have_posts()) : $more_issues->the_post(); ?>
                <?php
                    $m_my    = get_field('issue_month_year');
                    $m_auth  = get_field('author_name');
                    $m_thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                ?>
                <a href="<?php echo esc_url(get_the_permalink()); ?>" class="related-card">
                    <?php if ($m_thumb) : ?>
                    <img class="related-card-thumb"
                         src="<?php echo esc_url($m_thumb); ?>"
                         alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php else : ?>
                    <div class="related-card-thumb"></div>
                    <?php endif; ?>
                    <div class="related-card-body">
                        <p class="related-card-title"><?php echo esc_html(get_the_title()); ?></p>
                        <div class="related-card-meta">
                            <?php if ($m_auth) : ?>
                                <span><?php echo esc_html($m_auth); ?></span>
                            <?php endif; ?>
                            <?php if ($m_my) : ?>
                                <?php if ($m_auth) : ?><span class="dot">•</span><?php endif; ?>
                                <span><?php echo esc_html($m_my); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Subscribe CTA — dark purple box -->
        <div class="sidebar-section">
            <div class="subscribe-box">
                <p class="subscribe-box-title">சந்தா சேருங்கள்</p>
                <p class="subscribe-box-desc">
                    போதி முரசு இதழின் அனைத்து கட்டுரைகளையும் படிக்க சந்தா எடுக்கவும். மாத சந்தா ரூ.99 மட்டுமே.
                </p>
                <a href="<?php echo esc_url($checkout_url); ?>" class="subscribe-box-btn">
                    இப்போதே சந்தா சேருங்கள்
                </a>
            </div>
        </div>

    </aside><!-- /.article-sidebar -->

</div><!-- /.page-wrap -->

<?php wp_footer(); ?>
</body>
</html>
        <?php
        $output = ob_get_clean();
        echo $output;
        exit;
    }
}
add_action('template_redirect', 'magazine_single_redirect');