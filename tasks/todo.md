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
- [ ] Phase 8: 検証（php -l ✅、grep ✅、zip ✅、Local 動作確認 ⏳、Plugin Check ⏳）→ codex-rescue → PR
- [x] Phase 9: 返信ドラフト作成

## レビュー（完了時に記入）
