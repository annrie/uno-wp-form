# Unomoon Form

<p align="center">
  <!-- License -->
  <a href="LICENSE">
    <img src="https://img.shields.io/github/license/annrie/unomoon-form.svg" alt="License">
  </a>
  <!-- Stars -->
  <a href="https://github.com/annrie/unomoon-form/stargazers">
    <img src="https://img.shields.io/github/stars/annrie/unomoon-form.svg" alt="Stars">
  </a>
  <!-- Last commit -->
  <a href="https://github.com/annrie/unomoon-form/commits">
    <img src="https://img.shields.io/github/last-commit/annrie/unomoon-form.svg" alt="Last commit">
  </a>
</p>

Unomoon Form is a WordPress 7 compatible fork of MW WP Form.

Unomoon Form は、MW WP Form の開発停止を受けてフォークした、WordPress 7.0 検証済みのフォームプラグインです。

MW WP Form development has stopped, so this fork migrates the plugin to the `unomoon-form` namespace and keeps the shortcode-based form workflow available for current WordPress environments.

MW WP Form の開発が停止したため、このフォークではプラグインを `unomoon-form` 名前空間へ移行し、ショートコードベースのフォーム作成ワークフローを現在の WordPress 環境で利用できるようにしています。

## Features / 主な機能

- Shortcode-based form creation / ショートコードによるフォーム作成
- Confirmation screen support / 確認画面
- Same-page or separate-page transitions / 同一URLまたは個別URLでの画面遷移
- Validation rules / バリデーションルール
- Admin notification email and automatic reply email / 管理者宛メールと自動返信メール
- Inquiry data storage / 問い合わせデータ保存
- Chart display for saved inquiry data / 保存データのグラフ表示
- Japanese translation files / 日本語翻訳ファイル

## Namespace Changes / 名前空間の変更

This fork uses Unomoon Form identifiers.

このフォークでは Unomoon Form の識別子を使用します。

- Plugin slug: `unomoon-form`
- Post type: `unomoon-form`
- Shortcode prefix: `unomoonform_*`
- Hook prefix: `unomoonform_*`
- Frontend wrapper class: `.unomoon_form`

Existing MW WP Form data should be migrated intentionally before production use.

既存の MW WP Form データを利用する場合は、本番利用前に意図的に移行してください。

## Requirements / 動作要件

- WordPress 6.0 or later / WordPress 6.0 以上
- Tested up to WordPress 7.0 / WordPress 7.0 検証済み
- PHP 8.0 or later / PHP 8.0 以上

## Installation / インストール

1. Upload the `unomoon-form` directory to `wp-content/plugins/`.
2. Activate `Unomoon Form` in the WordPress admin.
3. Create or edit forms from the `Unomoon Form` admin menu.
4. Place the generated shortcode on a page.

1. `unomoon-form` ディレクトリを `wp-content/plugins/` にアップロードします。
2. WordPress管理画面で `Unomoon Form` を有効化します。
3. `Unomoon Form` の管理メニューからフォームを作成または編集します。
4. 生成されたショートコードを固定ページなどに配置します。

## Upstream / フォーク元

This project is forked from MW WP Form:

このプロジェクトは MW WP Form からフォークしています。

https://github.com/web-soudan/mw-wp-form

The fork exists because upstream development has stopped.

フォークした理由は、フォーク元の開発が停止したためです。

## Related Plugin / 関連プラグイン

Unomoon Form reCAPTCHA:

https://github.com/annrie/unomoon-form-recaptcha

## License / ライセンス

GPLv2 or later.

GPLv2 またはそれ以降。
