# WordPress.org 審査対応（Review ID: P0TDX368747HGN、2026-09-14）

計画本体: `~/.claude/plans/vectorized-inventing-swan.md`

## チェックリスト

- [x] Phase 0: ブランチ `feat/wordpress-org-review-1`（A・B）
- [x] Phase 1: リネーム Uno WP Form → Unomoon Form（A・B、識別子全部）
- [x] Phase 2: ヘッダ / 翻訳 / readme / package.sh / Version 5.1.6.2
- [x] Phase 3: ABSPATH ガード全ファイル
- [x] Phase 4: jQuery UI テーマ CSS 同梱、Google Charts → Chart.js
- [x] Phase 5: インライン script/style の enqueue 化
- [x] Phase 6: セキュリティ修正（セッション / CSRF / nonce / sanitize / escape / upload / debug log）
- [x] Phase 7: 移行スクリプト tools/migrate-from-uno-wp-form.php
- [ ] Phase 8: 検証（php -l ✅、grep ✅、zip ✅、Local 動作確認 ✅、Plugin Check ERROR 0 ✅、codex-rescue ✅）→ PR
- [x] Phase 9: 返信ドラフト作成

## レビュー（完了時に記入）

### 検証結果（2026-09-14、Local phantomoon）
- 移行スクリプト: dry-run → 本実行で post type 2 / postmeta 2 / option 3 / 投稿本文 3 件を移行、既存フォーム・問い合わせデータが新名称で表示
- フロント: 入力 → 確認 → 送信完了。`<b>` はテキストとして表示、`\` と `<` は保持（サニタイズは不正 UTF-8 / NUL 除去のみ、出力時エスケープ）
- 保存データ: `unomoon_6471` post type で保存、meta 値に二重スラッシュなし
- datepicker / monthpicker: `wp_add_inline_script` で初期化され、同梱 jQuery UI テーマで描画。外部 googleapis への参照なし
- チャート: Chart.js で pie / bar とも描画
- Plugin Check（配布 zip 展開版）: ERROR は検証用ディレクトリ名に起因する TextDomainMismatch のみ → 実質 0。WARNING はテンプレート内変数名・error_log 等の既存パターン
- codex レビュー: post type 接頭辞の 20 文字制限を指摘 → `unomoon_` に短縮して対応
