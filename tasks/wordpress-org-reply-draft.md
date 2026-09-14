# WordPress.org 審査への返信ドラフト

- 宛先: 受信メール（件名 `[WordPress Plugin Directory] Review in Progress: Uno WP Form`）への**返信**として送る（新規メール不可）
- Review ID: `P0TDX368747HGN` / スレッド ID `{#HS:3450274685-1119084#}` は返信ヘッダに自動で残る
- 送信前に "Add your plugin" ページで `packaged/unomoon-form-5.1.6.2.zip` をアップロードしておく
- 変更一覧は書かない（レビュー側が全体を再確認するため不要と明記されている）

---

Subject: Re: [WordPress Plugin Directory] Review in Progress: Uno WP Form

Hello,

Thank you for the review. I have addressed all of the reported issues and uploaded a new version (5.1.6.2) via the "Add your plugin" page.

Please reserve the new permalink/slug: **unomoon-form**
Display name: **Unomoon Form**

"Unomoon" is a coined word that I use as my own brand (it is also the name of my WordPress theme, github.com/annrie/unomoon); it is not derived from any trademark. The word "WP" has been removed from the name, the slug, the text domain and every internal identifier.

Two points of context for the re-review:

- The Google-hosted assets are gone. The inquiry-data chart now uses a bundled copy of Chart.js (MIT) and the jQuery UI theme CSS is bundled as well, so the plugin no longer loads anything from external servers.
- The bundled translation files were removed; translations will be handled through translate.wordpress.org.

Best regards,
annrie
