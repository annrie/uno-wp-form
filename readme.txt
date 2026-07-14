=== Uno WP Form ===
Tags: plugin, form, confirm, preview, shortcode, mail, chart, graph, html, contact form, form creation, form creator, form manager, form builder, custom form
Requires at least: 6.0
Requires PHP: 8.0
Tested up to: 7.0
Stable tag: 5.1.4-uno.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Uno WP Form is a WordPress 7 compatible fork of MW WP Form, created because MW WP Form development has stopped.

Uno WP Form は、MW WP Form の開発停止を受けてフォークした、WordPress 7.0 検証済みのフォームプラグインです。

== Description ==

Uno WP Form can create mail forms with confirmation screens using shortcodes.

Uno WP Form は、ショートコードで確認画面付きのメールフォームを作成できます。

This fork migrates plugin identifiers, post types, shortcode prefixes, hooks, assets, and admin labels to the `uno-wp-form` / `unoform_*` namespace while preserving the core form workflow.

このフォークでは、既存のフォーム作成フローを維持しながら、プラグイン識別子、投稿タイプ、ショートコード接頭辞、フック、アセット、管理画面ラベルを `uno-wp-form` / `unoform_*` 名前空間へ移行しています。

Features:

* Shortcode-based form creation
* Confirmation screen support
* Same-page or separate-page transitions
* Validation rules
* Admin notification email and automatic reply email
* Inquiry data storage
* Chart display for saved inquiry data
* Japanese translation files

主な機能:

* ショートコードによるフォーム作成
* 確認画面
* 同一URLまたは個別URLでの画面遷移
* バリデーションルール
* 管理者宛メールと自動返信メール
* 問い合わせデータ保存
* 保存データのグラフ表示
* 日本語翻訳ファイル

= Project =

GitHub: https://github.com/annrie/uno-wp-form

= Upstream =

Original upstream: https://github.com/web-soudan/mw-wp-form

This project is forked from MW WP Form because upstream development has stopped.

このプロジェクトは、MW WP Form の開発が停止したためフォークしたものです。

= Third-party resources =

Google Charts
Source: https://developers.google.com/chart/

== Installation ==

1. Upload the `uno-wp-form` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Create a form from the Uno WP Form admin screen.
4. Place the generated shortcode on a page.

インストール:

1. `uno-wp-form` フォルダを `/wp-content/plugins/` ディレクトリへアップロードします。
2. WordPress管理画面の「プラグイン」から有効化します。
3. Uno WP Form の管理画面でフォームを作成します。
4. 生成されたショートコードを固定ページなどに配置します。

== Frequently Asked Questions ==

= Where should I report issues? =

Please use GitHub Issues.

不具合報告や要望は GitHub Issues を利用してください。

https://github.com/annrie/uno-wp-form/issues

= Can I use MW WP Form shortcodes as-is? =

No. This fork uses the `unoform_*` shortcode namespace.

いいえ。このフォークでは `unoform_*` ショートコード名前空間を使用します。

== Screenshots ==

1. Form creation page.
2. Form item creation box.
3. Inquiry data storage.
4. Saved inquiry data list.
5. Saved inquiry data chart.

1. フォーム作成画面。
2. フォーム項目作成ボックス。
3. 問い合わせデータ保存。
4. 保存された問い合わせデータ一覧。
5. 保存された問い合わせデータのグラフ。
