---
description: Pull Requestを作成
allowed-tools: Bash(git:*), Bash(gh:*), Read
---

# Pull Request作成

現在のブランチからPull Requestを作成し、適切なタイトルと説明文を自動生成します。

## 実行内容

1. git差分を確認（`git status`, `git diff`）
2. コミット履歴を確認（`git log`）
3. タイトルと説明文を生成：
   - 概要
   - 変更内容（箇条書き）
   - 関連Issue
   - 影響範囲
   - 確認事項
4. `gh pr create` コマンドでPull Requestを作成
5. 作成されたPRのURLを報告

## Pull Requestテンプレート

- **日本語で記述**
- **「です・ます」調で統一**
- **箇条書きで変更点を整理**
- **関連Issue番号を含める**
- **影響範囲を明記**
- **確認事項を記載**

詳細は `.claude/skills/create-pull-request/SKILL.md` を参照してください。
