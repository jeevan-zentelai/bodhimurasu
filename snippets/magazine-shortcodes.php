<?php

/**
 * ============================================================
 * போதி முரசு — WORDPRESS MAGAZINE FUNCTIONS v17-final
 * Home page: https://bodhimurasu.org/
 * Month filter URL: https://bodhimurasu.org/?month=டிசம்பர்%202025&year=2025
 * ============================================================
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function bodhi_earlier_issues_url() {
    $page = get_page_by_path( 'earlier-issues' );
    $url  = $page ? get_permalink( $page->ID ) : home_url( '/earlier-issues/' );
    return apply_filters( 'bodhi_earlier_issues_url', $url );
}

function bodhi_normalize_cat( $cat ) {
    $cat          = trim( $cat );
    $editorial    = [ 'Editorial',    'தலையங்கம்' ];
    $dhamma_talks = [ 'Dhamma Talks', 'தம்ம உரைகள்' ];
    $katturaikal  = [ 'Articles',     'கட்டுரைகள்' ];
    $thiyanam     = [ 'Meditation',   'தியானம்' ];
    $events       = [ 'Events',       'நிகழ்வுகள்' ];
    if ( in_array( $cat, $editorial,    true ) ) return 'தலையங்கம்';
    if ( in_array( $cat, $dhamma_talks, true ) ) return 'தம்ம உரைகள்';
    if ( in_array( $cat, $katturaikal,  true ) ) return 'கட்டுரைகள்';
    if ( in_array( $cat, $thiyanam,     true ) ) return 'தியானம்';
    if ( in_array( $cat, $events,       true ) ) return 'நிகழ்வுகள்';
    return 'பிற';
}

function bodhi_cat_label( $cat ) {
    $map = [
        'Editorial'    => 'தலையங்கம்',
        'Dhamma Talks' => 'தம்ம உரைகள்',
        'Articles'     => 'கட்டுரைகள்',
        'Meditation'   => 'தியானம்',
        'Events'       => 'நிகழ்வுகள்',
        'தலையங்கம்'   => 'தலையங்கம்',
        'தம்ம உரைகள்' => 'தம்ம உரைகள்',
        'கட்டுரைகள்'  => 'கட்டுரைகள்',
        'தியானம்'     => 'தியானம்',
        'நிகழ்வுகள்'  => 'நிகழ்வுகள்',
        'பிற'          => 'பிற',
    ];
    return isset( $map[$cat] ) ? $map[$cat] : $cat;
}

function bodhi_enqueue_fonts() {
    wp_enqueue_style( 'bodhi-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400'
        . '&family=Noto+Serif+Tamil:wght@400;500;600;700'
        . '&family=Noto+Sans+Tamil:wght@400;500;600;700&display=swap',
        [], null );
}
add_action( 'wp_enqueue_scripts', 'bodhi_enqueue_fonts' );

function bodhi_global_styles() { ?>
<style id="bodhi-v17-styles">
:root{
    --lav-50:#f5f3ff;--lav-100:#ede9fe;--lav-200:#ddd6fe;--lav-300:#c4b5fd;
    --lav-400:#a78bfa;--lav-500:#8b5cf6;--lav-600:#7c3aed;--lav-700:#6d28d9;
    --lav-800:#5b21b6;--lav-900:#4c1d95;--lav-950:#2e1065;
    --white:#fff;
    --text-dark:#1e1b2e;--text-mid:#4a4560;--text-soft:#8b84a8;
    --r-sm:6px;--r-md:12px;
    --f-body:'Noto Serif Tamil','Tiro Tamil',serif;
    --f-ui:'Noto Sans Tamil',sans-serif;
    --tr:.22s ease;
}
.bodhi-page *,.bodhi-page *::before,.bodhi-page *::after{box-sizing:border-box;}
.bodhi-page body,.bodhi-page #page,.bodhi-page .site,.bodhi-page #content,
.bodhi-page .site-content,.bodhi-page #primary,.bodhi-page .content-area,
.bodhi-page #main,.bodhi-page .site-main,.bodhi-page article,.bodhi-page .hentry,
.bodhi-page .entry-header,.bodhi-page .entry-content,.bodhi-page .post-content,
.bodhi-page .page-content,.bodhi-page .wp-block-group,
.bodhi-page .wp-block-group__inner-container,.bodhi-page .wp-site-blocks,
.bodhi-page .wp-site-blocks>*:first-child,.bodhi-page .is-layout-flow>*:first-child,
.bodhi-page .site-main>article:first-child,.bodhi-page .site-main>.page:first-child{
    margin-top:0!important;padding-top:0!important;}
.bodhi-page .entry-content,.bodhi-page .post-content,.bodhi-page #primary,
.bodhi-page .content-area,.bodhi-page main#main,.bodhi-page .site-main,
.bodhi-page .elementor-section-boxed>.elementor-container,
.bodhi-page .elementor-widget-container{
    max-width:100%!important;width:100%!important;background:#faf8fd;}
.bodhi-page #secondary,.bodhi-page .widget-area,.bodhi-page aside.sidebar{display:none!important;}
.bodhi-wrap{font-family:var(--f-body);color:var(--text-dark);padding:0!important;margin:0!important;width:100%!important;max-width:100%!important;overflow-x:hidden;}
.bc{max-width:1200px;margin:0 auto;padding:0 32px;}
.bea-wrap{background:#faf8fd;width:100%;}

/* MASTHEAD */
.bm{display:block;position:relative;left:50%;right:50%;width:100vw!important;max-width:100vw!important;
    margin-left:-50vw!important;margin-right:-50vw!important;margin-top:0!important;margin-bottom:0!important;
    padding:38px 0 28px;text-align:center;
    background:linear-gradient(135deg,#2d1b4e 0%,#4a2d73 50%,#2d1b4e 100%);
    overflow:hidden;box-sizing:border-box;top:0;}
.bm::before{content:'';position:absolute;inset:0;pointer-events:none;
    background:radial-gradient(circle at 20% 50%,rgba(196,181,253,.08) 0%,transparent 50%),
               radial-gradient(circle at 80% 20%,rgba(139,92,246,.12) 0%,transparent 40%);}
.bm::after{content:'☸';position:absolute;right:-40px;top:50%;transform:translateY(-50%);
    font-size:220px;line-height:1;color:rgba(255,255,255,.025);pointer-events:none;}
.bm-inner{position:relative;z-index:1;}
.bm-inner-two{position:relative;z-index:1;margin-top:20px;}
.bm-motto{font-family:var(--f-ui);font-size:.7rem;letter-spacing:3px;color:var(--lav-300);margin-bottom:8px;text-transform:uppercase;}
.bm-title{font-family:var(--f-body);font-size:3.8rem;font-weight:700;color:#fff;line-height:1.15;margin-bottom:4px;text-shadow:0 4px 24px rgba(0,0,0,.3);}
.bm-sub{font-family:var(--f-ui);font-size:.78rem;color:var(--lav-300);letter-spacing:2px;margin-bottom:18px;}
.bm-meta{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;}
.bm-info{font-family:var(--f-ui);font-size:.8rem;color:rgba(255,255,255,.75);display:flex;align-items:center;gap:5px;}
.bm-sep{opacity:.3;}
.bm-selwrap{position:relative;}
.bm-selwrap::after{content:'▾';position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--lav-300);pointer-events:none;font-size:10px;}
.bm-sel{font-family:var(--f-ui);font-size:.78rem;font-weight:600;padding:6px 30px 6px 13px;
    border:1.5px solid rgba(196,181,253,.4);border-radius:30px;background:rgba(255,255,255,.1);
    color:#fff;appearance:none;cursor:pointer;transition:border-color var(--tr);min-width:180px;}
.bm-sel:hover{border-color:var(--lav-300);}
.bm-sel option{background:var(--lav-950);color:#fff;}
.bm-subbtn{font-family:var(--f-ui);font-size:.76rem;font-weight:700;background:var(--lav-500);
    color:#fff;padding:6px 16px;border-radius:30px;text-decoration:none;transition:background var(--tr);}
.bm-subbtn:hover{background:var(--lav-400);color:#fff;}
.bm-subbadge{font-family:var(--f-ui);font-size:.76rem;font-weight:700;background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.22);color:var(--lav-300);padding:4px 13px;border-radius:30px;}

/* CAROUSEL */
.bcar{background:#fff;padding:30px 0 36px;border-bottom:2px solid var(--lav-100);}
.bcar-wrap{position:relative;overflow:hidden;border-radius:10px;box-shadow:0 6px 32px rgba(80,60,180,.13);}
.bcar-track{display:flex;transition:transform .6s cubic-bezier(.4,0,.2,1);}
.bcar-slide{min-width:100%;display:grid;grid-template-columns:50% 1fr;height:390px;}
.bcar-body{background:#dfe2f0;padding:44px 48px 44px 56px;display:flex;flex-direction:column;
    justify-content:center;opacity:0;transform:translateX(-16px);
    transition:opacity .45s .12s ease,transform .45s .12s ease;}
.bcar-slide.is-active .bcar-body{opacity:1;transform:translateX(0);}
.bcar-section-label{font-family:var(--f-ui);font-size:.76rem;font-weight:600;color:var(--lav-700);
    letter-spacing:.6px;margin-bottom:14px;display:block;}
.bcar-body h2{font-family:var(--f-body);font-size:1.7rem;font-weight:700;color:#1a1a2e;line-height:1.42;margin:0 0 16px;}
.bcar-body p{font-family:var(--f-body);font-size:.875rem;color:#4a4570;line-height:1.85;margin:0 0 22px;
    max-width:340px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.bcar-readmore{font-family:var(--f-ui);font-size:.84rem;font-weight:700;color:var(--lav-700);
    text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:gap var(--tr),color var(--tr);}
.bcar-readmore:hover{color:var(--lav-500);gap:9px;}
.bcar-img{position:relative;overflow:hidden;background:#b0a8d0;}
.bcar-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s ease;}
.bcar-slide:hover .bcar-img img{transform:scale(1.04);}
.bcar-btn{position:absolute;top:50%;transform:translateY(-50%);width:36px;height:36px;border-radius:50%;
    background:rgba(255,255,255,.92);border:1.5px solid rgba(180,170,230,.5);color:var(--lav-800);
    font-size:1.15rem;cursor:pointer;z-index:20;display:flex;align-items:center;justify-content:center;
    box-shadow:0 2px 12px rgba(80,60,180,.18);transition:all var(--tr);}
.bcar-btn:hover{background:var(--lav-700);color:#fff;border-color:var(--lav-700);}
.bcar-btn.prev{left:8px;}.bcar-btn.next{right:8px;}
.bcar-dots{position:absolute;bottom:16px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:8px;z-index:20;}
.bcar-dot{width:10px;height:10px;border-radius:50%;background:rgba(150,140,210,.45);border:none;padding:0;cursor:pointer;transition:all .3s ease;flex-shrink:0;}
.bcar-dot.active{background:var(--lav-600);width:24px;border-radius:6px;}
.bcar-empty{display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px;height:390px;
    background:var(--lav-50);font-family:var(--f-body);font-size:.95rem;color:var(--text-soft);}
.bcar-empty span{font-size:2.5rem;opacity:.28;}

/* LAYOUT */
.bl-content{background:#faf8fd;}
.bl-wrap{display:flex;flex-direction:row;align-items:flex-start;width:100%;overflow:clip;}
.bl-sidebar{flex:0 0 200px;width:200px;min-width:200px;position:sticky;top:68px;align-self:flex-start;margin-bottom:32px;z-index:10;}
.bl-sidebar-hdr{display:none;}
.bl-tabs{display:flex;flex-direction:column;padding:18px 0;gap:2px;}
.bl-tab{font-family:var(--f-ui);font-size:.88rem;font-weight:400;color:#5a4f6b;
    padding:10px 18px 10px 12px;border:none;border-right:3px solid transparent;
    border-top-right-radius:10px;border-bottom-right-radius:10px;
    background:none;text-align:right;cursor:pointer;
    display:flex;align-items:center;justify-content:flex-end;gap:10px;
    transition:color var(--tr),background var(--tr),border-color var(--tr);
    width:100%;line-height:1.5;}
.bl-tab:hover{color:#2d2440;background:#f0ebf7;border-radius:10px;}
.bl-tab.active{color:#4c1d95;font-weight:700;background:#ede9fe;border-right-color:#6d28d9;border-radius:10px;}
.bl-tab-cnt{display:none;}
.bl-articles{flex:1 1 0;min-width:0;padding:32px 0 32px 36px;overflow:visible;}
.bl-sec{margin-bottom:48px;}
.bl-sec.bl-hidden{display:none;}
.bl-sec.bl-fade{animation:blFade .3s ease forwards;}
@keyframes blFade{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.bl-sec-hdr{font-family:var(--f-body);font-size:1.2rem;font-weight:700;color:var(--text-dark);
    margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid #c4b5fd;display:inline-block;}
.bl-sec-title{font-family:var(--f-body);font-size:1.2rem;font-weight:700;color:var(--text-dark);margin:0;}
.bl-prev-block{margin-bottom:48px;display:none;}
.bl-prev-block.bl-fade{animation:blFade .3s ease forwards;}
.bl-prev-hdr{display:flex;align-items:center;gap:14px;margin-bottom:10px;padding:20px 0 10px 0;border-top:1px dashed #d4c8e2;}
.bl-prev-hdr-title{font-family:var(--f-body);font-size:1.1rem;font-weight:600;color:var(--text-mid);margin:0;white-space:nowrap;}
.bl-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
.bl-card{background:var(--white);border-radius:10px;overflow:hidden;border:1px solid #e8e2f4;
    box-shadow:0 1px 4px rgba(45,27,78,.07);display:flex;flex-direction:column;
    text-decoration:none;color:inherit;transition:transform var(--tr),box-shadow var(--tr),border-color var(--tr);cursor:pointer;}
.bl-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(45,27,78,.13);border-color:#c4b5fd;}
.bl-card-img{height:130px;overflow:hidden;background:var(--lav-100);position:relative;flex-shrink:0;}
.bl-card-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;}
.bl-card:hover .bl-card-img img{transform:scale(1.06);}
.bl-card-badge{position:absolute;top:8px;left:8px;display:inline-flex;align-items:center;justify-content:center;
    width:26px;height:26px;border-radius:5px;background:rgba(0,0,0,.5);color:#fff!important;}
.bl-card-badge svg{width:13px;height:13px;fill:#fff;display:block;}
.bl-card-body{padding:12px 14px 14px;flex:1;display:flex;flex-direction:column;}
.bl-card-cat{font-family:var(--f-ui);font-size:.65rem;font-weight:600;color:#9b8eb8;margin-bottom:5px;}
.bl-card-title{font-family:var(--f-body);font-size:.92rem;font-weight:700;color:var(--text-dark);line-height:1.5;margin-bottom:6px;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.bl-card-excerpt{font-size:.76rem;color:var(--text-soft);line-height:1.65;margin-bottom:8px;flex:1;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.bl-card-foot{display:flex;justify-content:space-between;align-items:center;padding-top:8px;
    border-top:1px solid #f0eaf8;font-family:var(--f-ui);font-size:.67rem;color:var(--text-soft);}
.bl-card-author{font-weight:600;color:var(--text-mid);}
.bl-sec-empty{grid-column:1/-1;text-align:center;padding:36px;background:var(--lav-50);
    border-radius:var(--r-md);border:1px dashed var(--lav-200);}
.bl-sec-empty p{font-family:var(--f-body);color:var(--text-soft);font-size:.88rem;margin-top:6px;}

/* EARLIER ISSUES */
.bea-content-width{max-width:1200px;margin:0 auto;background:transparent;}
.bea-content{margin:0 auto;background:transparent;}
.bea-nav-back{color:#fff;text-decoration:none;font-size:13px;font-family:var(--f-ui);
    padding:7px 14px;border:1.5px solid rgba(255,255,255,0.3);border-radius:6px;
    transition:all 0.2s ease;background:rgba(255,255,255,0.08);display:inline-flex;align-items:center;font-weight:600;}
.bea-nav-back:hover{background:rgba(255,255,255,0.18);border-color:rgba(255,255,255,0.8);transform:translateY(-1px);}
.bea-accordion{background:#fff;border:1px solid #e2deef;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(45,27,78,.08);}
.bea-year-row{display:flex;align-items:center;gap:12px;padding:15px 20px;cursor:pointer;
    background:#fff;border-bottom:1px solid #f0eaf8;user-select:none;transition:background var(--tr);position:relative;}
.bea-year-row:last-of-type{border-bottom:none;}
.bea-year-row:hover:not(.is-open){background:#f9f7fc;}
.bea-year-row.is-open{background:linear-gradient(90deg,var(--lav-700) 0%,var(--lav-600) 100%);border-bottom-color:var(--lav-700);}
.bea-year-row.is-open .bea-year-text{color:#fff;}
.bea-year-row.is-open .bea-year-badge{background:rgba(255,255,255,.22);color:#fff;}
.bea-year-row.is-open .bea-year-caret{color:rgba(255,255,255,.85);transform:rotate(90deg);}
.bea-year-text{font-family:var(--f-ui);font-size:.95rem;font-weight:700;color:var(--text-dark);min-width:52px;transition:color var(--tr);}
.bea-year-badge{font-family:var(--f-ui);font-size:.66rem;font-weight:700;color:var(--lav-700);background:var(--lav-100);padding:2px 9px;border-radius:20px;transition:all var(--tr);}
.bea-year-caret{margin-left:auto;font-size:.78rem;color:#aaa7c4;transition:transform .28s ease,color var(--tr);display:inline-block;line-height:1;}
.bea-year-panel{display:none;background:#f5f4fb;border-bottom:1px solid #e8e0f0;padding:24px 20px 28px;}
.bea-year-panel.is-open{display:block;animation:beaIn .24s ease;}
@keyframes beaIn{from{opacity:0;transform:translateY(-5px);}to{opacity:1;transform:translateY(0);}}
.bea-month-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
.bea-month-card{display:flex;flex-direction:column;text-decoration:none;color:inherit;
    border:1px solid #e8e2f4;border-radius:10px;overflow:hidden;background:#fff;
    box-shadow:0 1px 4px rgba(45,27,78,.07);transition:transform var(--tr),box-shadow var(--tr),border-color var(--tr);cursor:pointer;}
.bea-month-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(45,27,78,.13);border-color:#c4b5fd;}
.bea-month-thumb{width:100%;height:130px;overflow:hidden;background:var(--lav-100);position:relative;flex-shrink:0;}
.bea-month-thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;}
.bea-month-card:hover .bea-month-thumb img{transform:scale(1.06);}
.bea-month-thumb-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;opacity:.22;}
.bea-month-badge{position:absolute;top:8px;left:8px;display:inline-flex;align-items:center;justify-content:center;
    width:26px;height:26px;border-radius:5px;background:rgba(0,0,0,.5);color:#fff!important;}
.bea-month-badge svg{width:13px;height:13px;fill:#fff;display:block;}
.bea-month-info{padding:12px 14px 14px;flex:1;display:flex;flex-direction:column;}
.bea-month-lbl{font-family:var(--f-ui);font-size:.65rem;font-weight:600;color:#9b8eb8;margin-bottom:5px;}
.bea-month-title{font-family:var(--f-body);font-size:.92rem;font-weight:700;color:var(--text-dark);line-height:1.5;margin-bottom:6px;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.bea-month-excerpt{font-size:.76rem;color:var(--text-soft);line-height:1.65;margin-bottom:8px;flex:1;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.bea-month-footer{display:flex;justify-content:space-between;align-items:center;padding-top:8px;
    border-top:1px solid #f0eaf8;font-family:var(--f-ui);font-size:.67rem;color:var(--text-soft);}
.bea-month-author{font-weight:600;color:var(--text-mid);}
.bea-empty{grid-column:1/-1;text-align:center;padding:36px 20px;background:var(--lav-50);border-radius:8px;border:1.5px dashed var(--lav-200);}
.bea-empty p{font-family:var(--f-body);color:var(--text-soft);font-size:.88rem;margin-top:6px;}

/* RESPONSIVE */
@media(max-width:1100px){
    .bcar-slide{grid-template-columns:44% 1fr;height:360px;}
    .bcar-body{padding:32px 36px 32px 44px;}
    .bcar-body h2{font-size:1.48rem;}
    .bl-grid{grid-template-columns:repeat(2,1fr);}
    .bea-month-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:860px){
    .bm-title{font-size:2.6rem;}
    .bcar{padding:14px 0 18px;}
    .bcar-slide{grid-template-columns:1fr;height:auto;}
    .bcar-img{height:220px;order:-1;}
    .bcar-body{padding:20px 20px 32px;opacity:1;transform:none;}
    .bcar-body h2{font-size:1.22rem;}
    .bl-wrap{flex-direction:column;}
    .bl-sidebar{flex:0 0 auto;width:100%;min-width:0;position:static;border-bottom:1px solid #ece9f8;}
    .bl-sidebar-hdr{display:block;background:linear-gradient(135deg,var(--lav-700),var(--lav-900));padding:12px 18px;}
    .bl-sidebar-hdr p{font-family:var(--f-ui);font-size:.62rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--lav-300);margin-bottom:2px;}
    .bl-sidebar-hdr h3{font-family:var(--f-body);font-size:.92rem;font-weight:600;color:#fff;}
    .bl-tabs{flex-direction:row;overflow-x:auto;margin:10px 16px;gap:6px;scrollbar-width:none;}
    .bl-tabs::-webkit-scrollbar{display:none;}
    .bl-tab{white-space:nowrap;text-align:center;justify-content:center;flex:0 0 auto;padding:7px 16px;font-size:.82rem;border-radius:20px;border-right:none;background:rgba(255,255,255,.6);color:#4a4560;}
    .bl-tab.active{background:#6d28d9;color:#fff;font-weight:600;border-radius:20px;border-right-color:transparent;}
    .bl-articles{padding:20px 14px;}
    .bl-card-img{height:115px;}
    .bea-content{padding:0 14px 40px;}
    .bea-month-grid{grid-template-columns:repeat(2,1fr);gap:12px;}
    .bea-year-panel{padding:14px 14px 20px;}
    .bea-year-row{padding:12px 14px;}
}
@media(max-width:560px){
    .bm-title{font-size:2rem;}
    .bl-grid{grid-template-columns:1fr;}
    .bc{padding:0 14px;}
    .bl-card-img{height:150px;}
    .bea-month-grid{grid-template-columns:1fr;gap:20px;}
    .bea-month-thumb{height:180px;}
}
</style>
<?php }
add_action( 'wp_head', 'bodhi_global_styles', 10 );

/* ============================================================
   FIX: Register month + year as valid WP query vars.
============================================================ */
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'month';
    $vars[] = 'year';
    return $vars;
} );

/* ============================================================
   FIX: Stop WP canonical redirect when month/year params present.
============================================================ */
add_filter( 'redirect_canonical', function( $redirect_url, $requested_url ) {
    if ( isset( $_GET['month'] ) || isset( $_GET['year'] ) ) {
        return false;
    }
    return $redirect_url;
}, 10, 2 );

/* ============================================================
   FIX: Add bodhi-page CSS class on front page.
============================================================ */
add_filter( 'body_class', function( $classes ) {
    if ( is_front_page() && ! in_array( 'bodhi-page', $classes, true ) ) {
        $classes[] = 'bodhi-page';
    }
    return $classes;
}, 20 );

function bodhi_get_all_issues() {
    static $cache = null;
    if ( $cache !== null ) return $cache;
    $q = new WP_Query([
        'post_type'      => 'magazine_issue',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ]);
    $list = [];
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $id      = get_the_ID();
            $month   = function_exists('get_field') ? ( get_field('issue_month_year',$id) ?: '' ) : '';
            $ts      = $month ? strtotime($month) : get_the_date('U',$id);
            $raw_cat = function_exists('get_field') ? trim( get_field('category_label',$id) ?: '' ) : '';
            $list[]  = [
                'id'       => $id,
                'title'    => get_the_title($id),
                'link'     => get_permalink($id),
                'thumb'    => get_the_post_thumbnail_url($id,'large')        ?: '',
                'thumb_md' => get_the_post_thumbnail_url($id,'medium_large') ?: '',
                'month'    => $month,
                'desc'     => function_exists('get_field')
                                ? ( get_field('short_description',$id) ?: get_the_excerpt() )
                                : get_the_excerpt(),
                'cat'      => $raw_cat,
                'cat_group'=> bodhi_normalize_cat( $raw_cat ),
                'author'   => function_exists('get_field') ? trim( get_field('author_name',$id) ?: '' ) : '',
                'paid'     => function_exists('get_field') ? (bool)get_field('requires_subscription',$id) : false,
				'hero_banner' => function_exists('get_field') ? (bool)get_field('hero_banner',$id) : false,
                'vis'      => function_exists('get_field') ? ( get_field('magazine_visibility',$id) ?: 'free' ) : 
				'free',
                'ts'       => $ts,
            ];
        }
        wp_reset_postdata();
    }
    usort( $list, fn($a,$b) => $b['ts'] - $a['ts'] );
    $cache = $list;
    return $cache;
}

function bodhi_unique_months( $issues ) {
    $seen = [];
    foreach ( $issues as $i )
        if ( $i['month'] && ! in_array($i['month'],$seen,true) ) $seen[] = $i['month'];
    return $seen;
}

function bodhi_unique_cats( $issues ) {
    /* ── Updated group order with கட்டுரைகள் and தியானம் ── */
    $order = [ 'தலையங்கம்', 'தம்ம உரைகள்', 'கட்டுரைகள்', 'தியானம்', 'நிகழ்வுகள்', 'பிற' ];
    $seen  = [];
    foreach ( $issues as $i )
        if ( $i['cat_group'] && ! in_array($i['cat_group'],$seen,true) ) $seen[] = $i['cat_group'];
    $result = [];
    foreach ( $order as $o ) if ( in_array($o,$seen,true) ) $result[] = $o;
    return $result;
}

function bodhi_badge_lock_svg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff"><path d="M12 1C9.24 1 7 3.24 7 6v1H5a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-2V6c0-2.76-2.24-5-5-5zm0 2c1.65 0 3 1.35 3 3v1H9V6c0-1.65 1.35-3 3-3zm0 9a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>';
}
function bodhi_badge_user_svg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff"><path d="M12 12c2.7 0 5-2.3 5-5S14.7 2 12 2 7 4.3 7 7s2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v1a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-1c0-3.3-6.7-5-10-5z"/></svg>';
}

function bodhi_card_html( $d ) {
    $badge = '';
    if ( $d['paid'] )             $badge = '<span class="bl-card-badge">'.bodhi_badge_lock_svg().'</span>';
    elseif ( $d['vis']==='login') $badge = '<span class="bl-card-badge">'.bodhi_badge_user_svg().'</span>';
    $img = $d['thumb_md']
        ? '<img src="'.esc_url($d['thumb_md']).'" alt="'.esc_attr($d['title']).'" loading="lazy">'
        : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;opacity:.22;">&#128218;</div>';
    ob_start(); ?>
    <a href="<?php echo esc_url($d['link']); ?>" class="bl-card"
       data-cat="<?php echo esc_attr($d['cat']); ?>"
       data-cat-group="<?php echo esc_attr($d['cat_group']); ?>"
       data-month="<?php echo esc_attr($d['month']); ?>"
       data-ts="<?php echo esc_attr($d['ts']); ?>">
        <div class="bl-card-img"><?php echo $img.$badge; ?></div>
        <div class="bl-card-body">
            <?php if($d['cat']): ?>
            <span class="bl-card-cat"><?php echo esc_html(bodhi_cat_label($d['cat'])); ?></span>
            <?php endif; ?>
            <h3 class="bl-card-title"><?php echo esc_html($d['title']); ?></h3>
            <p class="bl-card-excerpt"><?php echo esc_html(wp_trim_words($d['desc'],16,'...')); ?></p>
            <div class="bl-card-foot">
                <span class="bl-card-author"><?php echo esc_html($d['author']); ?></span>
                <span><?php echo esc_html($d['month']); ?></span>
            </div>
        </div>
    </a>
    <?php return ob_get_clean();
}

/* ============================================================
   [bodhi_masthead]
============================================================ */
function bodhi_masthead_sc( $atts = [] ) {
    $atts       = shortcode_atts(['archive'=>''], $atts);
    $is_archive = ! empty($atts['archive']);
    $issues     = bodhi_get_all_issues();
    $months     = bodhi_unique_months($issues);
    $latest     = $months[0] ?? '';
    $iss_num    = (!empty($issues)&&function_exists('get_field')) ? get_field('issue_number',$issues[0]['id']) : '';
    $sub_pid    = 782;
    $checkout   = function_exists('wc_get_checkout_url') ? wc_get_checkout_url().'?add-to-cart='.$sub_pid : '#';
    $has_sub    = false;
    if ( is_user_logged_in() ) {
        $uid = get_current_user_id();
        if ( current_user_can('administrator')||current_user_can('editor') ) { $has_sub=true; }
        elseif ( function_exists('wc_customer_bought_product') ) {
            $ui = get_userdata($uid);
            $has_sub = wc_customer_bought_product($ui?$ui->user_email:'',$uid,$sub_pid);
        }
    }

    $url_month   = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : '';
    $display_lbl = $url_month ?: ( $latest ?: 'அனைத்து இதழ்கள்' );

    $top5        = array_slice($months, 0, 5);
    $has_earlier = count($months) > 5;
    $earlier_url = bodhi_earlier_issues_url();
    $mag_url     = home_url('/');

    ob_start(); ?>
    <header class="bm" id="bodhiMasthead">
        <div class="bc bm-inner">
            <p class="bm-motto">சீலம் &bull; சமாதி &bull; பஞ்ஞா</p>
            <h1 class="bm-title">போதி முரசு</h1>
            <p class="bm-sub">மாத இதழ்</p>
            <div class="bm-meta">
                <span class="bm-info" id="bDateLbl">
                    <?php if($iss_num && !$is_archive): ?>இதழ் <?php echo esc_html($iss_num); ?> <span class="bm-sep">|</span><?php endif; ?>
                    <?php echo esc_html($display_lbl); ?>
                </span>
                <?php if($has_sub): ?>
                    <span class="bm-subbadge">&#10003; சந்தாதாரர்</span>
                <?php else: ?>
                    <span class="bm-info">ரூ.99/-</span>
                <?php endif; ?>
                <?php if(!empty($months)): ?>
                <div class="bm-selwrap">
                    <select class="bm-sel" id="bodhiDateSel"
                            data-earlier-url="<?php echo esc_attr($earlier_url); ?>"
                            data-mag-url="<?php echo esc_attr($mag_url); ?>"
                            data-is-archive="<?php echo $is_archive ? '1' : '0'; ?>">
                        <?php if($is_archive): ?>
                        <option value="__magazine__">நடப்பு இதழ்</option>
                        <?php endif; ?>
                        <option value="" <?php selected($url_month, ''); ?>>அனைத்து இதழ்கள்</option>
                        <?php foreach($top5 as $m): ?>
                        <option value="<?php echo esc_attr($m); ?>" <?php selected($url_month, $m); ?>><?php echo esc_html($m); ?></option>
                        <?php endforeach; ?>
                        <?php if($has_earlier): ?>
                        <option value="__earlier__">முந்தைய இதழ்கள் &rarr;</option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if(!$has_sub): ?>
                <a href="<?php echo esc_url($checkout); ?>" class="bm-subbtn">&#128274; சந்தா சேர &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php return ob_get_clean();
}
add_shortcode('bodhi_masthead','bodhi_masthead_sc');

function bodhi_carousel_sc($atts) {
    $atts   = shortcode_atts(['slides'=>5],$atts);
    $issues = bodhi_get_all_issues();
    $hero   = array_values(array_filter($issues, fn($i) => !empty($i['hero_banner'])));
	$slides = array_slice($hero, 0, absint($atts['slides']));
    if(empty($slides)) return '';
    ob_start(); ?>
    <section class="bcar" id="bodhiCarSec">
        <div class="bc">
        <div class="bcar-wrap" id="bodhiCarWrap">
            <div class="bcar-track" id="bodhiCarTrack">
            <?php foreach($slides as $i=>$s):
                $desc = wp_trim_words($s['desc'],22,'...');
            ?>
            <div class="bcar-slide <?php echo $i===0?'is-active':''; ?>"
                 data-month="<?php echo esc_attr($s['month']); ?>">
                <div class="bcar-body">
                    <?php if($s['cat']): ?>
                    <span class="bcar-section-label"><?php echo esc_html(bodhi_cat_label($s['cat'])); ?></span>
                    <?php endif; ?>
                    <h2><?php echo esc_html($s['title']); ?></h2>
                    <p><?php echo esc_html($desc); ?></p>
                    <a href="<?php echo esc_url($s['link']); ?>" class="bcar-readmore">மேலும் படிக்க &rarr;</a>
                </div>
                <div class="bcar-img">
                    <?php if($s['thumb']): ?>
                    <img src="<?php echo esc_url($s['thumb']); ?>" alt="<?php echo esc_attr($s['title']); ?>"
                         loading="<?php echo $i===0?'eager':'lazy'; ?>">
                    <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--lav-200),var(--lav-400));display:flex;align-items:center;justify-content:center;font-size:70px;opacity:.25;">&#128218;</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <button class="bcar-btn prev" id="bodhiCarPrev" aria-label="முந்தைய">&#8249;</button>
            <button class="bcar-btn next" id="bodhiCarNext" aria-label="அடுத்து">&#8250;</button>
            <div class="bcar-dots" id="bodhiCarDots">
                <?php for($d=0;$d<count($slides);$d++): ?>
                <button class="bcar-dot <?php echo $d===0?'active':''; ?>" data-index="<?php echo $d; ?>"></button>
                <?php endfor; ?>
            </div>
        </div>
        </div>
    </section>
    <?php return ob_get_clean();
}
add_shortcode('bodhi_carousel','bodhi_carousel_sc');

function bodhi_articles_sc() {
    $issues     = bodhi_get_all_issues();
    $cat_groups = bodhi_unique_cats($issues);
    ob_start(); ?>
    <section class="bl-content">
        <div class="bc"><div class="bl-wrap">
            <aside class="bl-sidebar">
                <div class="bl-sidebar-hdr">
                    <p>உள்ளடக்கம்</p>
                    <h3>இதழ் பகுதிகள்</h3>
                </div>
                <div class="bl-tabs" id="blTabs">
                    <button class="bl-tab active" data-cat="all" data-slug="all">
                        அனைத்தும்
                        <span class="bl-tab-cnt" id="blCntAll"><?php echo count($issues); ?></span>
                    </button>
                    <?php foreach($cat_groups as $grp):
                        $slug = sanitize_title($grp);
                        $cnt  = count(array_filter($issues,fn($x)=>$x['cat_group']===$grp));
                    ?>
                    <button class="bl-tab" data-cat="<?php echo esc_attr($grp); ?>" data-slug="<?php echo esc_attr($slug); ?>">
                        <?php echo esc_html(bodhi_cat_label($grp)); ?>
                        <span class="bl-tab-cnt" id="blCnt_<?php echo esc_attr($slug); ?>"><?php echo $cnt; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </aside>
            <div class="bl-articles" id="blArticles">
                <?php if(empty($issues)): ?>
                <div class="bl-sec-empty" style="margin:40px 0;">
                    <span style="font-size:2.5rem;opacity:.3;">☸</span>
                    <p>இதழ்கள் எதுவும் இல்லை.</p>
                </div>
                <?php else:
                foreach($cat_groups as $grp):
                    $slug       = sanitize_title($grp);
                    $grp_issues = array_values(array_filter($issues,fn($x)=>$x['cat_group']===$grp));
                ?>
                <div class="bl-sec" data-cat="<?php echo esc_attr($grp); ?>" data-slug="<?php echo esc_attr($slug); ?>" id="blSec_<?php echo esc_attr($slug); ?>">
                    <div class="bl-sec-hdr"><h2 class="bl-sec-title"><?php echo esc_html(bodhi_cat_label($grp)); ?></h2></div>
                    <div class="bl-grid" id="blGrid_<?php echo esc_attr($slug); ?>">
                        <?php foreach($grp_issues as $iss) echo bodhi_card_html($iss); ?>
                    </div>
                    <div class="bl-sec-empty" id="blEmpty_<?php echo esc_attr($slug); ?>" style="display:none;margin-top:12px;">
                        <span style="font-size:1.8rem;opacity:.3;">☸</span>
                        <p>தேர்ந்தெடுத்த தேதியில் கட்டுரைகள் இல்லை.</p>
                    </div>
                </div>
                <div class="bl-prev-block" data-cat="<?php echo esc_attr($grp); ?>" data-slug="<?php echo esc_attr($slug); ?>" id="blPrev_<?php echo esc_attr($slug); ?>">
                    <div class="bl-prev-hdr"><h3 class="bl-prev-hdr-title">முந்தைய இதழ்களிலிருந்து — <?php echo esc_html(bodhi_cat_label($grp)); ?></h3></div>
                    <div class="bl-grid" id="blPrevGrid_<?php echo esc_attr($slug); ?>"></div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div></div>
    </section>
    <script id="blIssuesJson" type="application/json">
    <?php
    echo wp_json_encode(array_map(fn($x) => [
        'id'           => $x['id'],
        'title'        => $x['title'],
        'link'         => $x['link'],
        'thumb'        => $x['thumb'],
        'thumbMd'      => $x['thumb_md'],
        'month'        => $x['month'],
        'desc'         => wp_trim_words($x['desc'],16,'...'),
        'cat'          => $x['cat'],
        'catDisplay'   => bodhi_cat_label($x['cat']),
        'catGroup'     => $x['cat_group'],
        'catGroupLabel'=> bodhi_cat_label($x['cat_group']),
        'catGroupSlug' => sanitize_title($x['cat_group']),
        'author'       => $x['author'],
        'paid'         => $x['paid'],
        'vis'          => $x['vis'],
		'heroBanner'   => $x['hero_banner'],
        'ts'           => $x['ts'],
    ], $issues), JSON_UNESCAPED_UNICODE);
    ?>
    </script>
    <?php return ob_get_clean();
}
add_shortcode('bodhi_articles','bodhi_articles_sc');

/* ============================================================
   EARLIER ISSUES PAGE
============================================================ */
function bodhi_earlier_issues_page_sc() {
    $issues = bodhi_get_all_issues();
    if ( empty( $issues ) ) {
        return '<div class="bm"><div class="bea-content" style="text-align:center">'
             . '<p style="font-family:var(--f-body);color:var(--text-soft);">இதழ்கள் எதுவும் இல்லை.</p></div></div>';
    }

    $years   = [];
    $by_year = [];

    foreach ( $issues as $iss ) {
        $y = '';
        if ( preg_match( '/\b(20\d{2}|19\d{2})\b/', $iss['month'], $m ) ) $y = $m[1];
        if ( ! $y ) $y = date( 'Y', $iss['ts'] );

        if ( ! in_array( $y, $years, true ) ) $years[] = $y;

        $month_val = $iss['month'];
        if ( $month_val && ! isset( $by_year[ $y ][ $month_val ] ) ) {
            $by_year[ $y ][ $month_val ] = [
                'label' => $month_val,
                'thumb' => $iss['thumb_md'] ?: $iss['thumb'],
                'ts'    => $iss['ts'],
            ];
        }
    }

    rsort( $years );
    $first_year = $years[0] ?? '';
    $mag_url    = home_url( '/' );

    ob_start(); ?>
    <header class="bm" id="bodhiMasthead">
        <div class="bc bm-inner-two">
            <p class="bm-motto">சீலம் &bull; சமாதி &bull; பஞ்ஞா</p>
            <h1 class="bm-title">போதி முரசு</h1>
            <p class="bm-sub">மாத இதழ்</p>
            <div class="bea-nav">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bea-nav-back">&#8592; நடப்பு இதழ்</a>
            </div>
        </div>
    </header>

    <div class="bea-wrap">
        <div class="bea-content-width">
            <div class="bea-content">
                <div class="bea-accordion" id="beaAccordion">
                <?php foreach ( $years as $y ) :
                    $yr_months = $by_year[ $y ] ?? [];
                    if ( empty( $yr_months ) ) continue;
                    uasort( $yr_months, fn( $a, $b ) => $b['ts'] - $a['ts'] );
                    $is_open  = ( $y === $first_year );
                    $panel_id = 'beaPanel_' . esc_attr( $y );
                ?>
                    <div class="bea-year-row<?php echo $is_open ? ' is-open' : ''; ?>"
                         data-year="<?php echo esc_attr( $y ); ?>"
                         aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                         aria-controls="<?php echo $panel_id; ?>"
                         role="button" tabindex="0">
                        <span class="bea-year-text"><?php echo esc_html( $y ); ?></span>
                        <span class="bea-year-badge"><?php echo count( $yr_months ); ?> மாதம்</span>
                        <span class="bea-year-caret">&#9654;</span>
                    </div>

                    <div class="bea-year-panel<?php echo $is_open ? ' is-open' : ''; ?>"
                         id="<?php echo $panel_id; ?>" role="region">
                        <div class="bea-month-grid bea-month-grid--compact">
                        <?php foreach ( $yr_months as $month_val => $md ) :
                            $tile_url = add_query_arg( [
                                'month' => $month_val,
                                'year'  => $y,
                            ], $mag_url );
                        ?>
                            <a href="<?php echo esc_url( $tile_url ); ?>" class="bea-month-tile">
                                <div class="bea-month-tile-thumb">
                                    <?php if ( $md['thumb'] ) : ?>
                                    <img src="<?php echo esc_url( $md['thumb'] ); ?>"
                                         alt="<?php echo esc_attr( $md['label'] ); ?>" loading="lazy">
                                    <?php else : ?>
                                    <div class="bea-month-tile-ph">&#128218;</div>
                                    <?php endif; ?>
                                    <div class="bea-month-tile-overlay">
                                        <span class="bea-month-tile-lbl"><?php echo esc_html( $md['label'] ); ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
    .bea-month-grid--compact{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
    .bea-month-tile{display:block;text-decoration:none;border-radius:10px;overflow:hidden;
        border:1px solid #e8e2f4;background:#fff;box-shadow:0 1px 4px rgba(45,27,78,.07);
        transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease;cursor:pointer;}
    .bea-month-tile:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(45,27,78,.15);border-color:#c4b5fd;}
    .bea-month-tile-thumb{position:relative;width:100%;padding-top:72%;overflow:hidden;background:#ede9fe;}
    .bea-month-tile-thumb img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;}
    .bea-month-tile:hover .bea-month-tile-thumb img{transform:scale(1.07);}
    .bea-month-tile-ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:2.2rem;opacity:.22;}
    .bea-month-tile-overlay{position:absolute;bottom:0;left:0;right:0;padding:10px 10px 9px;
        background:linear-gradient(to top,rgba(30,15,60,.72) 0%,transparent 100%);}
    .bea-month-tile-lbl{font-family:var(--f-ui);font-size:.8rem;font-weight:700;color:#fff;
        letter-spacing:.3px;text-shadow:0 1px 4px rgba(0,0,0,.4);}
    @media(max-width:860px){.bea-month-grid--compact{grid-template-columns:repeat(3,1fr);gap:12px;}}
    @media(max-width:560px){.bea-month-grid--compact{grid-template-columns:repeat(2,1fr);gap:10px;}}
    </style>

    <script>
    (function(){
        var acc = document.getElementById('beaAccordion');
        if(!acc) return;
        function openRow(row){
            var p = document.getElementById(row.getAttribute('aria-controls'));
            row.classList.add('is-open'); row.setAttribute('aria-expanded','true');
            if(p) p.classList.add('is-open');
        }
        function closeRow(row){
            var p = document.getElementById(row.getAttribute('aria-controls'));
            row.classList.remove('is-open'); row.setAttribute('aria-expanded','false');
            if(p) p.classList.remove('is-open');
        }
        acc.addEventListener('click', function(e){
            var row = e.target.closest('.bea-year-row');
            if(!row) return;
            var wasOpen = row.classList.contains('is-open');
            acc.querySelectorAll('.bea-year-row').forEach(closeRow);
            if(!wasOpen) openRow(row);
        });
        acc.addEventListener('keydown', function(e){
            if(e.key==='Enter'||e.key===' '){
                var row = e.target.closest('.bea-year-row');
                if(row){ e.preventDefault(); row.click(); }
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bodhi_earlier_issues_page', 'bodhi_earlier_issues_page_sc' );

function bodhi_magazine_page_sc() {
    ob_start();
    echo do_shortcode('[bodhi_masthead]');
    echo '<div class="bodhi-wrap">';
    echo do_shortcode('[bodhi_carousel slides="5"]');
    echo do_shortcode('[bodhi_articles]');
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('bodhi_magazine_page','bodhi_magazine_page_sc');

add_filter('body_class', function($c){
    if(is_page('magazine')||is_page('magazine-2')||is_singular('magazine_issue')||is_page('earlier-issues'))
        $c[]='bodhi-page';
    return $c;
});

add_action('wp_body_open', function(){
    if(!is_page(['magazine','magazine-2','earlier-issues'])
       && !is_singular('magazine_issue')
       && !is_front_page()) return;
    echo '<style>
        body.bodhi-page{margin-top:0!important;padding-top:0!important;}
        body.bodhi-page .site-content,body.bodhi-page #content,
        body.bodhi-page .entry-content>*:first-child,
        body.bodhi-page .post-content>*:first-child,
        body.bodhi-page .page-content>*:first-child{margin-top:0!important;padding-top:0!important;}
    </style>';
});

/* ============================================================
   JAVASCRIPT — URL ?month= and ?year= support
============================================================ */
function bodhi_scripts() { ?>
<script>
(function(){
'use strict';
var dataEl = document.getElementById('blIssuesJson');
if(!dataEl) return;
var ALL = JSON.parse(dataEl.textContent || dataEl.innerHTML);
ALL.sort(function(a,b){ return b.ts - a.ts; });

var activeMonth = '', activeCat = 'all';

/* ── Updated GROUP_ORDER with கட்டுரைகள் and தியானம் ── */
var GROUP_ORDER = ['தலையங்கம்','தம்ம உரைகள்','கட்டுரைகள்','தியானம்','நிகழ்வுகள்','பிற'];

var ALL_GROUPS = (function(){
    var out = [];
    GROUP_ORDER.forEach(function(g){
        if(ALL.some(function(i){ return i.catGroup === g; })) out.push(g);
    });
    return out;
})();
var GROUP_SLUG = {};
ALL.forEach(function(i){ if(i.catGroup && i.catGroupSlug) GROUP_SLUG[i.catGroup] = i.catGroupSlug; });

function h(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
var LOCK = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fff"><path d="M12 1C9.24 1 7 3.24 7 6v1H5a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-2V6c0-2.76-2.24-5-5-5zm0 2c1.65 0 3 1.35 3 3v1H9V6c0-1.65 1.35-3 3-3zm0 9a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>';
var USER = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fff"><path d="M12 12c2.7 0 5-2.3 5-5S14.7 2 12 2 7 4.3 7 7s2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v1a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-1c0-3.3-6.7-5-10-5z"/></svg>';

function cardHtml(iss){
    var badge = iss.paid ? '<span class="bl-card-badge">'+LOCK+'</span>'
              : (iss.vis==='login' ? '<span class="bl-card-badge">'+USER+'</span>' : '');
    var img = iss.thumbMd
        ? '<img src="'+h(iss.thumbMd)+'" alt="'+h(iss.title)+'" loading="lazy">'
        : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;opacity:.22;">&#128218;</div>';
    return '<a href="'+h(iss.link)+'" class="bl-card" data-cat="'+h(iss.cat)+'" data-cat-group="'+h(iss.catGroup)+'" data-month="'+h(iss.month)+'" data-ts="'+iss.ts+'">'
        +'<div class="bl-card-img">'+img+badge+'</div>'
        +'<div class="bl-card-body">'+(iss.cat?'<span class="bl-card-cat">'+h(iss.catDisplay)+'</span>':'')
        +'<h3 class="bl-card-title">'+h(iss.title)+'</h3>'
        +'<p class="bl-card-excerpt">'+h(iss.desc)+'</p>'
        +'<div class="bl-card-foot"><span class="bl-card-author">'+h(iss.author)+'</span><span>'+h(iss.month)+'</span></div>'
        +'</div></a>';
}

/* ── CAROUSEL ── */
var cTrack = document.getElementById('bodhiCarTrack'),
    cDots  = document.getElementById('bodhiCarDots'),
    cPrev  = document.getElementById('bodhiCarPrev'),
    cNext  = document.getElementById('bodhiCarNext'),
    cCur   = 0, cTimer;

function buildCarousel(items){
    if(!cTrack) return;
    clearInterval(cTimer);
    var list = items.slice(0,5);
    if(!list.length){
        cTrack.innerHTML = '<div class="bcar-empty"><span>☸</span><p>இந்த தேர்வில் இதழ்கள் இல்லை.</p></div>';
        if(cDots) cDots.innerHTML = '';
        return;
    }
    var html = '';
    list.forEach(function(s,i){
        var img = s.thumb
            ? '<img src="'+h(s.thumb)+'" alt="'+h(s.title)+'" loading="lazy">'
            : '<div style="width:100%;height:100%;background:linear-gradient(135deg,#ddd6fe,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:70px;opacity:.25;">&#128218;</div>';
        html += '<div class="bcar-slide'+(i===0?' is-active':'')+'" data-month="'+h(s.month)+'">'
              + '<div class="bcar-body">'+(s.catDisplay?'<span class="bcar-section-label">'+h(s.catDisplay)+'</span>':'')
              + '<h2>'+h(s.title)+'</h2><p>'+h(s.desc)+'</p><a href="'+h(s.link)+'" class="bcar-readmore">மேலும் படிக்க →</a>'
              + '</div><div class="bcar-img">'+img+'</div></div>';
    });
    cTrack.innerHTML = html;
    cTrack.style.transform = 'translateX(0)';
    cCur = 0;
    if(cDots){
        var d='';
        for(var i=0;i<list.length;i++) d+='<button class="bcar-dot'+(i===0?' active':'')+'" data-index="'+i+'"></button>';
        cDots.innerHTML = d;
        bindDots();
    }
    if(list.length > 1) startAuto();
}
function goSlide(n){
    var ss = cTrack ? cTrack.querySelectorAll('.bcar-slide') : [];
    var dd = cDots  ? cDots.querySelectorAll('.bcar-dot')   : [];
    if(!ss.length) return;
    if(ss[cCur]) ss[cCur].classList.remove('is-active');
    if(dd[cCur]) dd[cCur].classList.remove('active');
    cCur = ((n % ss.length) + ss.length) % ss.length;
    if(cTrack) cTrack.style.transform = 'translateX(-'+(cCur*100)+'%)';
    if(ss[cCur]) ss[cCur].classList.add('is-active');
    if(dd[cCur]) dd[cCur].classList.add('active');
}
function startAuto(){ cTimer = setInterval(function(){ goSlide(cCur+1); }, 9000); }
function resetAuto(){ clearInterval(cTimer); startAuto(); }
function bindDots(){
    if(!cDots) return;
    cDots.querySelectorAll('.bcar-dot').forEach(function(d){
        d.addEventListener('click',function(){ goSlide(+this.dataset.index); resetAuto(); });
    });
}
if(cPrev) cPrev.addEventListener('click', function(){ goSlide(cCur-1); resetAuto(); });
if(cNext) cNext.addEventListener('click', function(){ goSlide(cCur+1); resetAuto(); });
var cWrap = document.getElementById('bodhiCarWrap');
if(cWrap){
    cWrap.addEventListener('mouseenter', function(){ clearInterval(cTimer); });
    cWrap.addEventListener('mouseleave', function(){
        var s = cTrack ? cTrack.querySelectorAll('.bcar-slide') : [];
        if(s.length > 1) startAuto();
    });
}
var tx = 0;
if(cTrack){
    cTrack.addEventListener('touchstart', function(e){ tx = e.changedTouches[0].screenX; },{passive:true});
    cTrack.addEventListener('touchend',   function(e){
        var d = tx - e.changedTouches[0].screenX;
        if(Math.abs(d)>50){ goSlide(d>0?cCur+1:cCur-1); resetAuto(); }
    },{passive:true});
}
bindDots();
(function(){ var s=cTrack?cTrack.querySelectorAll('.bcar-slide'):[];if(s.length>1)startAuto(); })();

/* ── FILTER ENGINE ── */
function strTS(s){ return s ? (new Date(s)).getTime() : 0; }
function applyFilters(){
    ALL_GROUPS.forEach(function(grp){
        var slug      = GROUP_SLUG[grp] || '';
        var secEl     = document.getElementById('blSec_'+slug);
        var prevEl    = document.getElementById('blPrev_'+slug);
        var gridEl    = document.getElementById('blGrid_'+slug);
        var prevGridEl= document.getElementById('blPrevGrid_'+slug);
        var emptyEl   = document.getElementById('blEmpty_'+slug);
        var showSec   = (activeCat==='all') || (activeCat===grp);
        if(!showSec){
            if(secEl)  secEl.classList.add('bl-hidden');
            if(prevEl) prevEl.style.display = 'none';
            return;
        }
        if(secEl) secEl.classList.remove('bl-hidden');
        var currentIssues, prevIssues = [];
        if(activeCat !== 'all'){
            if(!activeMonth){
                var gm = [];
                ALL.forEach(function(i){ if(i.catGroup===grp && gm.indexOf(i.month)===-1) gm.push(i.month); });
                gm.sort(function(a,b){ return strTS(b)-strTS(a); });
                var newest = gm[0] || '';
                currentIssues = ALL.filter(function(i){ return i.catGroup===grp && i.month===newest; });
                prevIssues    = ALL.filter(function(i){ return i.catGroup===grp && i.month!==newest; });
            } else {
                currentIssues = ALL.filter(function(i){ return i.catGroup===grp && i.month===activeMonth; });
                prevIssues    = ALL.filter(function(i){ return i.catGroup===grp && i.month!==activeMonth; });
            }
        } else {
            currentIssues = ALL.filter(function(i){ return i.catGroup===grp && (!activeMonth||i.month===activeMonth); });
        }
        if(gridEl)   gridEl.innerHTML   = currentIssues.length ? currentIssues.map(cardHtml).join('') : '';
        if(emptyEl)  emptyEl.style.display = currentIssues.length ? 'none' : 'block';
        if(prevEl && prevGridEl){
            if(prevIssues.length > 0){
                prevGridEl.innerHTML = prevIssues.map(cardHtml).join('');
                prevEl.style.display = 'block';
                prevEl.classList.remove('bl-fade');
                void prevEl.offsetWidth;
                prevEl.classList.add('bl-fade');
            } else {
                prevEl.style.display = 'none';
            }
        }
        if(secEl){ secEl.classList.remove('bl-fade'); void secEl.offsetWidth; secEl.classList.add('bl-fade'); }
    });
    var ac = document.getElementById('blCntAll');
    if(ac) ac.textContent = ALL.filter(function(i){ return !activeMonth || i.month===activeMonth; }).length;
    ALL_GROUPS.forEach(function(grp){
        var slug = GROUP_SLUG[grp] || '';
        var el   = document.getElementById('blCnt_'+slug);
        if(!el) return;
        el.textContent = ALL.filter(function(i){ return i.catGroup===grp && (!activeMonth||i.month===activeMonth); }).length;
    });
    buildCarousel(ALL.filter(function(i){
    return i.heroBanner && (!activeMonth || i.month===activeMonth);
}));
}

/* ── DATE SELECTOR ── */
var dateSel = document.getElementById('bodhiDateSel');
var dateLbl = document.getElementById('bDateLbl');
if(dateSel){
    dateSel.addEventListener('change', function(){
        var val      = this.value;
        var isArchive= (this.dataset.isArchive === '1');
        var magUrl   = this.dataset.magUrl || '/';
        if(val === '__earlier__'){ window.location.href = this.dataset.earlierUrl || '/earlier-issues/'; return; }
        if(val === '__magazine__'){ window.location.href = magUrl; return; }
        if(isArchive){
            window.location.href = val ? magUrl + '?month=' + encodeURIComponent(val) + '&year=' + encodeURIComponent(val.match(/\d{4}/)?.[0]||'') : magUrl;
            return;
        }
        activeMonth = val;
        if(dateLbl) dateLbl.textContent = activeMonth || 'அனைத்து இதழ்கள்';

        var newUrl = window.location.pathname;
        if(val){
            var yr = (val.match(/\b(\d{4})\b/) || [])[1] || '';
            newUrl += '?month=' + encodeURIComponent(val);
            if(yr) newUrl += '&year=' + encodeURIComponent(yr);
        }
        window.history.replaceState(null, '', newUrl);

        applyFilters();
        var cs = document.getElementById('bodhiCarSec');
        if(cs) cs.scrollIntoView({behavior:'smooth', block:'start'});
    });
}

/* ── CATEGORY TABS ── */
var blTabs = document.getElementById('blTabs');
if(blTabs){
    blTabs.addEventListener('click', function(e){
        var tab = e.target.closest('.bl-tab');
        if(!tab) return;
        activeCat = tab.dataset.cat;
        blTabs.querySelectorAll('.bl-tab').forEach(function(t){ t.classList.remove('active'); });
        tab.classList.add('active');
        applyFilters();
        var ba = document.getElementById('blArticles');
        if(ba) ba.scrollIntoView({behavior:'smooth', block:'start'});
    });
}

/* ── INITIAL LOAD ── */
var urlParams = new URLSearchParams(window.location.search);
var urlMonth  = urlParams.get('month') || '';
if(urlMonth){
    activeMonth = urlMonth;
    if(dateLbl) dateLbl.textContent = urlMonth;
    if(dateSel){
        for(var oi = 0; oi < dateSel.options.length; oi++){
            if(dateSel.options[oi].value === urlMonth){ dateSel.selectedIndex = oi; break; }
        }
    }
}
var hasData = ALL.some(function(i){ return i.catGroup && i.month; });
if(hasData){
    applyFilters();
} else {
    var el = document.getElementById('blCntAll');
    if(el) el.textContent = ALL.length;
    buildCarousel(ALL.filter(function(i){ return i.heroBanner; }));
}

})();
</script>
<?php }
add_action( 'wp_footer', 'bodhi_scripts' );