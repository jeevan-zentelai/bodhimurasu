<?php

function bodhi_myaccount_style() {
    if (!is_account_page()) return;
    ?>
    <style>
    .woocommerce-account .u-columns.col2-set { display: block !important; }
    .woocommerce-account .u-column1,
    .woocommerce-account .u-column2 { display: none !important; width: 100% !important; float: none !important; }
    .woocommerce-account .u-column1.active,
    .woocommerce-account .u-column2.active { display: block !important; }
    .woocommerce-account .entry-title { display: none !important; }
    .woocommerce-account .entry-content,
    .woocommerce-account #primary { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }

    .bodhi-auth-wrap {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: #f5f0e8;
    }
    .bodhi-auth-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        border: 1px solid #e8dece;
    }
    .bodhi-auth-logo {
        background: #1a1a2e;
        padding: 24px;
        text-align: center;
    }
    .bodhi-auth-logo span {
        font-family: "Cinzel", serif;
        font-size: 22px;
        font-weight: 800;
        color: #c9a84c;
        letter-spacing: 3px;
        display: block;
    }
    .bodhi-auth-logo p {
        font-size: 11px;
        color: rgba(255,255,255,0.4);
        margin: 4px 0 0;
        letter-spacing: 1px;
    }
    .bodhi-auth-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-bottom: 2px solid #f0e8d8;
    }
    .bodhi-tab-btn {
        padding: 16px;
        background: none;
        border: none;
        font-family: "Cinzel", serif;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #999;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }
    .bodhi-tab-btn:hover { color: #c9a84c; }
    .bodhi-tab-btn.active {
        color: #1a1a2e;
        border-bottom-color: #c9a84c;
        background: #fffdf8;
    }
    .bodhi-auth-body { padding: 32px; }
    .bodhi-auth-body .woocommerce-form-login,
    .bodhi-auth-body .woocommerce-form-register {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .bodhi-auth-body label {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #555 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin-bottom: 6px !important;
        display: block !important;
    }
    .bodhi-auth-body input[type="text"],
    .bodhi-auth-body input[type="email"],
    .bodhi-auth-body input[type="password"] {
        width: 100% !important;
        padding: 12px 16px !important;
        border: 1.5px solid #e0d5c5 !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        color: #333 !important;
        background: #fdf8f2 !important;
        transition: border 0.2s !important;
        box-sizing: border-box !important;
        margin-bottom: 18px !important;
    }
    .bodhi-auth-body input:focus {
        border-color: #c9a84c !important;
        outline: none !important;
        background: white !important;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12) !important;
    }
    .bodhi-auth-body button[type="submit"],
    .bodhi-auth-body .woocommerce-form-login__submit,
    .bodhi-auth-body .woocommerce-form-register__submit {
        width: 100% !important;
        background: #c9a84c !important;
        color: #0f0e0b !important;
        border: none !important;
        padding: 14px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        letter-spacing: 2px !important;
        text-transform: uppercase !important;
        cursor: pointer !important;
        transition: background 0.2s, transform 0.2s !important;
        margin-top: 6px !important;
    }
    .bodhi-auth-body button[type="submit"]:hover {
        background: #f0d080 !important;
        transform: translateY(-2px) !important;
    }
    .bodhi-auth-body .woocommerce-form__label-for-checkbox {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 13px !important;
        color: #888 !important;
        margin: 0 0 20px !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
    }
    .bodhi-auth-body .woocommerce-LostPassword { text-align: center; margin-top: 16px; }
    .bodhi-auth-body .woocommerce-LostPassword a,
    .bodhi-auth-body a { color: #8b3a1a !important; font-size: 13px !important; text-decoration: none !important; }
    .bodhi-auth-body a:hover { text-decoration: underline !important; color: #c9a84c !important; }
    .bodhi-auth-body .woocommerce-privacy-policy-text {
        font-size: 11px !important; color: #aaa !important;
        line-height: 1.6 !important; margin-bottom: 16px !important;
    }
    .woocommerce-account .woocommerce-message,
    .woocommerce-account .woocommerce-error,
    .woocommerce-account .woocommerce-info {
        max-width: 460px !important; margin: 20px auto !important; border-radius: 8px !important;
    }
    .woocommerce-account .woocommerce { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
    .woocommerce-account .woocommerce-MyAccount-navigation {
        background: #1a1a2e !important; border-radius: 12px !important; padding: 20px 0 !important;
    }
    .woocommerce-account .woocommerce-MyAccount-navigation ul { list-style: none !important; margin: 0 !important; padding: 0 !important; }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li a {
        display: block !important; padding: 12px 20px !important;
        color: rgba(255,255,255,0.65) !important; text-decoration: none !important;
        font-size: 14px !important; transition: all 0.2s !important;
        border-left: 3px solid transparent !important;
    }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li a:hover,
    .woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active a {
        color: #c9a84c !important; border-left-color: #c9a84c !important;
        background: rgba(201,168,76,0.08) !important;
    }
    .woocommerce-account .woocommerce-MyAccount-content {
        background: white !important; border-radius: 12px !important;
        padding: 30px !important; border: 1px solid #e8dece !important;
    }
    @media(max-width:480px) {
        .bodhi-auth-body { padding: 24px 20px; }
        .bodhi-auth-card { border-radius: 12px; }
    }
    </style>
    <?php
}
add_action('wp_head', 'bodhi_myaccount_style');


function bodhi_myaccount_tabs_js() {
    if (!is_account_page()) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var colSet = document.querySelector('.col2-set');
        if (!colSet) return;

        var col1 = colSet.querySelector('.u-column1');
        var col2 = colSet.querySelector('.u-column2');
        if (!col1 || !col2) return;

        var wrap = document.createElement('div');
        wrap.className = 'bodhi-auth-wrap';

        var card = document.createElement('div');
        card.className = 'bodhi-auth-card';

        var logo = document.createElement('div');
        logo.className = 'bodhi-auth-logo';
        logo.innerHTML = '<span>BODHI MURASU</span><p>போதி முரசு</p>';

        var tabs = document.createElement('div');
        tabs.className = 'bodhi-auth-tabs';
        tabs.innerHTML =
            '<button class="bodhi-tab-btn active" data-tab="login">&#128274; உள்நுழைக</button>' +
            '<button class="bodhi-tab-btn" data-tab="register">&#128100; பதிவு செய்க</button>';

        var body = document.createElement('div');
        body.className = 'bodhi-auth-body';

        col1.classList.add('active');
        col2.classList.remove('active');

        body.appendChild(col1);
        body.appendChild(col2);

        card.appendChild(logo);
        card.appendChild(tabs);
        card.appendChild(body);
        wrap.appendChild(card);

        colSet.parentNode.insertBefore(wrap, colSet);
        colSet.parentNode.removeChild(colSet);

        tabs.querySelectorAll('.bodhi-tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                tabs.querySelectorAll('.bodhi-tab-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');

                if (btn.dataset.tab === 'login') {
                    col1.classList.add('active');
                    col2.classList.remove('active');
                } else {
                    col2.classList.add('active');
                    col1.classList.remove('active');
                }
            });
        });

        if (window.location.hash === '#register' || window.location.search.indexOf('register') > -1) {
            tabs.querySelectorAll('.bodhi-tab-btn')[1].click();
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'bodhi_myaccount_tabs_js');