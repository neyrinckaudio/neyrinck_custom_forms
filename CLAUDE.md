# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Neyrinck Custom Forms is a WordPress plugin (PHP) that registers shortcodes rendering the custom forms used on the Neyrinck website: product downloads, contact/support, newsletter subscriptions, dealer subscriptions, online activation, and VCP trial registration. There is no build system, package manager, or test suite — deployment is by copying the plugin directory into a WordPress `wp-content/plugins/` install and activating it.

The README notes the code is legacy and "needs to be modernized." Expect procedural PHP, global state, and inline HTML/JS mixed with PHP.

## Architecture

### Plugin bootstrap
- [neyrinck-custom-forms.php](neyrinck-custom-forms.php) is the WordPress plugin entry point. It defines `NCF_PLUGIN_DIR`, registers an activation hook (`NCF_install`) that creates the `{$wpdb->prefix}ncf_settings` table (one row holding `db_server`, `db_user`, `db_password`, `db_name`), and instantiates `Neyrinck_Custom_Forms`.
- [includes/class-neyrinck-custom-forms.php](includes/class-neyrinck-custom-forms.php) is the core class. It loads dependencies, wires the admin menu hook via the loader, and enqueues the form JS files from `shortcode/scripts/` on `wp_enqueue_scripts`.
- [includes/class-neyrinck-custom-forms-loader.php](includes/class-neyrinck-custom-forms-loader.php) is a tiny action/filter registration helper in the standard WP boilerplate style.

### Admin settings
- [admin/class-neyrinck-custom-forms-admin.php](admin/class-neyrinck-custom-forms-admin.php) adds the "Neyrinck Custom Forms" admin menu page where the external database connection (server / user / password / db name) is configured. Other code reads these via `get_settings()` and stores them as `$GLOBALS['ncf_database'|'ncf_user'|'ncf_password'|'ncf_server']` in [shortcode/shortcode.php](shortcode/shortcode.php). This is the license/user database used by activation, trial, and download forms — it is separate from the WordPress database.

### Shortcodes
[shortcode/shortcode.php](shortcode/shortcode.php) is the central registration point. Each `add_shortcode` entry maps a tag to a wrapper function that buffers and includes a template file from `shortcode/` or `shortcode/scripts/`. Key tags:

- `NCF_PRODUCT_DOWNLOAD_FORM` → `download-form.php` (takes a `product` attribute)
- `NCF_THANK_YOU_FOR_DOWNLOADING_SPILL` / `_VCP` → `scripts/sms_spill.php` / `scripts/sms_vcp.php`
- `NCF_ECHO_LATEST_VCP_DOWNLOAD_LINKS` → `scripts/vcp-download-links.php`
- `NCF_SUPPORT_FORM` / `NCF_CONTACT_FORM` → `support-form.php` / `contact-form.php`
- `NCF_NEWSLETTER_SUBSCRIPTION_FORM` → `scripts/subscription_form_reCaptcha.php`
- `NCF_DEALERNEWS_SUBSCRIPTION_FORM` → `scripts/dealer-subscribe.php`
- `NCF_ONLINE_ACTIVATION` / `_ACTIVATION2` → `scripts/activate.php` (the activation flow uses the `scripts/activation*.php` files and `NeyrinckActivation.php`)
- `NCF_VCP_TRIAL` / `_TRIAL_DEV` → `scripts/vcptrial.php` (uses `vcpTrialSubmitAccountForm.php`)

When adding or modifying a shortcode, register it here and follow the same `ob_start` / `include` / `ob_get_contents` pattern.

### Eden / wp-edenremote integration
- [includes/class-neyrinck-custom-forms-eden.php](includes/class-neyrinck-custom-forms-eden.php) wraps the external `WPEdenRemote` class (provided by the companion `wp-edenremote` WordPress plugin) with methods like `findUserByAccountId` and `findUserLicenseBySKU`. Every call checks `method_exists('WPEdenRemote', …)` so the plugin degrades gracefully if `wp-edenremote` is not installed.
- Per the README, the activation and VCP trial forms were ported (Feb 2025) to go through `wp-edenremote` instead of talking to the license DB directly. New license/account logic should generally go through this wrapper rather than direct SQL.

### Legacy direct-DB path
- `shortcode/scripts/database.php`, `dbFunctions.php`, and `ilokFunctions.php` still contain the older direct-MySQL access code used by parts of the plugin that have not been migrated to Eden. The database credentials come from the admin settings page described above. A previous fix removed a hardcoded `main.` database prefix — be careful not to reintroduce hardcoded DB names when editing these files.

### Third-party libraries (vendored, not managed by a package manager)
- `shortcode/scripts/twilio-php-master/` — Twilio PHP SDK used by the SMS download confirmation flow (`sms_spill.php`, `sms_vcp.php`, `SMS_download_app_form.php`).
- `shortcode/scripts/recaptchalib.php` — reCAPTCHA helper used by subscription/contact/support forms.
- `shortcode/scripts/countries.json` + `typeahead.js` — used by country pickers in forms.

## Development notes

- There is no build, lint, or test tooling. "Running" the plugin means installing it into a WordPress site and activating it; "testing" means exercising the shortcodes on pages that embed them.
- The plugin version lives in the header comment of [neyrinck-custom-forms.php](neyrinck-custom-forms.php) (currently `2.1`). Note that `Neyrinck_Custom_Forms::$version` in the core class is an unrelated stale `'1.0.0'` string — the plugin-header version is the source of truth.
- When editing form templates, remember they are included inside `ob_start()` wrappers from `shortcode.php`, so anything `echo`ed becomes the shortcode output. Do not call `exit`/`die` from template code paths that should return HTML.
- JS for the forms lives in `shortcode/scripts/*.js` and is enqueued globally in `load_js()` in the core class — new form scripts should be registered there.
