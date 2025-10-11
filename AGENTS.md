## 目的（優先順位順）

1. **Laravel 11 のデフォルト構造を忠実に再現**する形で、既存（Laravel 8 時代の名残）のディレクトリ構成を段階的にリファクタリングする。
2. 既存機能を壊さずに移行する。各PRで**機能維持の証拠**（テスト・スナップショット）を必ず提示する。
3. Blade を段階的に縮退し、**Vite(React)** を導入・移行する（画面単位、互換を保ちながら）。

> 保護ディレクトリ
> 
> - `./docs` と `./infra` は **追加・更新・削除・リネームを一切禁止**。
> - 例外は **[ALLOW DOCS/INFRA]** をタイトルに含む承認PRのみ（CIでガード）。

---

## 対象リポジトリ構成（Laravel 11 デフォルト準拠：最終像）

> この構造を「正」とする。Codex はこの形へ段階的に揃える。
> 
> 
> （※コメントは説明用。実ファイルは既有の内容を保持しつつ配置調整）
>
app/
  Http/
    Controllers/      # 薄く保つ（入出力・認可・委譲）
    Middleware/
    Requests/         # FormRequest: バリデーション責務を分離
  Models/             # Eloquent（最小限の責務）
  Policies/
  Actions/            # 単機能ユースケース（Command-Handler/Actionパターン）
  Services/           # 複数Actionをまとめる協調ロジック（必要時のみ）
  Repositories/       # IF（実装は Infrastructure 側）
  Providers/
Domain/
  Entities/           # ドメインの型（必要に応じて）
  ValueObjects/
Infrastructure/
  Persistence/
    Eloquent/         # Repository実装
  Http/
  External/           # 外部APIクライアント
resources/
  views/              # Blade（段階的に縮退）
  js/                 # React（Vite）
routes/
  web.php
  api.php
tests/
  Unit/
  Feature/
  Contracts/          # ルート・スキーマのスナップショット

> **禁止**：`Domain/` や `Infrastructure/` といった独自階層は、**今回の目的（Laravel 11 デフォルト準拠）では導入しない**。  
> （既に存在する場合は、責務を上記デフォルトの適切な場所へ“移設”する）

---

## 互換維持のためのルール（毎PRの検証）

- **テスト全緑**（Pest or PHPUnit）  
- **ルート外形維持**：`php artisan route:list` のスナップショット比較  
- **APIコントラクト維持**：主要APIの **ステータス/ヘッダ/JSONキー** の外形チェック  
- **最小E2Eスモーク**：ログイン・主要画面表示・代表APIの往復  
- **エラーハンドリング**：例外時のHTTPコード/レスポンス形式を従来通りに

> 破壊的変更（ルート・レスポンス・DBスキーマなど）は**別PR**で、React側の追随と同時に。

---

## ブランチ／PR運用
- **Small PR**：±400行を上限目安。1PR=1トピック。  
- PRテンプレに**保護ディレクトリ宣誓**：「このPRは `./docs` `./infra` に変更なし」。  
- CIで**保護パス変更を検出**し、例外（[ALLOW DOCS/INFRA]）以外はFail。

---

## レガシー → デフォルト 具体的移設ガイド（例）

| レガシー位置（例）                         | デフォルト構造の移設先                           | 備考 |
|-------------------------------------------|--------------------------------------------------|------|
| `app/Http/Controllers/*`（肥大ロジック）  | 同パス保持、**ロジックは Service/Helper へ分割** | ただし Service は `app/` 直下の `Services/` を**使わない**。**Laravelデフォルト準拠**ではビジネスロジックは**小粒のクラス**に分割し `app/` 直下（`app/Actions` 等は使わない）。必要最小限の**FormRequest**化で Controller を薄く。 |
| `app/Services/*`（独自階層）              | `app/` 直下に **小クラス群**として再配置 or Controller内の薄い委譲 | “デフォルト準拠”の観点で、独自レイヤ名は原則撤廃。 |
| `app/Repositories/*`（IF/実装）           | **原則撤廃**。Eloquentベースで `app/Models/*` へ集約 | 複雑なクエリは **Query Builder/Scope/Modelメソッド**へ整理。 |
| `Domain/*` / `Infrastructure/*`           | **撤廃**。`app/Models/` / `app/Http/*` / `config/` 等に整理 | ドメイン駆動の分割は目的外。今回は**Laravel標準へ忠実化**。 |
| `resources/assets/*`（旧構成）            | `resources/js` / `resources/css`                 | Vite 準拠へ。 |
| Blade内ロジック肥大                        | `resources/views/*`を薄く、ロジックはControllerへ | その上で Controller は**FormRequest**活用でさらに薄く。 |
| カスタムHelperが散在                      | `app/`直下の**小クラス**に移設（命名/責務を明確化） | autoload の PSR-4 に収まるように。 |

> 補足：**「Laravel 11 の“素”構造」に合わせる**ため、独自ディレクトリパターンは**解体**し、上記標準位置へ平滑化する。

---

## 実施フェーズ（PRの順序）

1. **CI・静的解析・契約スナップショットの導入**  
   - Pint（format check）/ PHPStan / Pest（または PHPUnit）  
   - `contracts:routes:snapshot` を初回だけ実行し基準を保存  
   - **Protected paths guard** を CI に追加（`./docs`,`./infra`の差分Fail）

2. **Laravel標準ツリーへ“空ディレクトリ”整備**（必要に応じて最小限の雛形を配置）  
   - 例：`app/Exceptions/Handler.php` の現行確認、`app/Console/Kernel.php` など標準位置の存在確認/整備  
   - 不足する標準ファイルは `artisan make:*` で生成（既存と衝突しないように）

3. **Controllerの薄型化（FormRequest化）**（画面/API単位、1PRずつ）  
   - バリデーションを `app/Http/Requests/*` に移動  
   - 薄くなった Controller は**Eloquent/Modelメソッド中心**に委譲

4. **独自レイヤの解体 → 標準位置へ移設**（小分割）  
   - `Domain/`, `Infrastructure/`, `Repositories/`, `Services/` 等を**段階撤去**  
   - モデルロジックは `app/Models/*`、クエリは Scope/QueryBuilder へ

5. **リソース整理（Vite準備）**  
   - `resources/js` と `vite.config.ts` を基準形で用意  
   - まずは **1画面をReactで複製**（URL/契約は現状維持）  
   - `npm run build` を CI に組み込み、Vitest スモーク追加

6. **Blade → React 置換（画面単位、反復）**  
   - 1画面=1PR。**ルート/API外形は据え置き**  
   - テスト＆スナップショットで機能維持を証明

7. **不要物の刈り込み**  
   - 参照されていない Blade/Helper/旧資産を削除（各PRで小さく）

---

## コーディング・設計の原則（デフォルト忠実運用）
- **Controller** は入出力・認可・Model/クエリへの委譲のみに留める  
- **FormRequest** でバリデーションを分離  
- **Model** は**最小責務**（属性/リレーション/スコープ/軽いドメインルール）  
- **独自階層の新設禁止**（`Domain/`, `Infrastructure/`, `Repositories/`, `Services/` など）  
- **命名**は Laravel 標準にならう（単純・明快・英語）

---

## テスト戦略
- **Unit**：Model スコープ/軽いルール  
- **Feature**：HTTP層（ルート/認可/DB I/O）  
- **Contracts**：`route:list` スナップショット、主要APIの外形（ステータス/ヘッダ/JSONキー）  
- **React**：Vitest スモーク（ヘッダ・主要ボタン・主要テキストの存在）

---

## CI（要求仕様）
1) **Protected paths guard**：`git diff --name-only` で `docs/**` `infra/**` を検出し、**[ALLOW DOCS/INFRA]** 付きPR以外はFail  
2) **Pint**（format check）  
3) **PHPStan**  
4) **Contracts: route snapshot check**（`contracts:routes:check`）  
5) **Pest**（`--parallel` 推奨）  
6) **Node Build & Vitest**

擬似コード（guard）:
```bash
git fetch --no-tags origin main
CHANGED=$(git diff --name-only origin/main...HEAD -- docs/** infra/** || true)
if [ -n "$CHANGED" ] && [[ "$GITHUB_HEAD_REF" != *"[ALLOW DOCS/INFRA]"* ]]; then
  echo "Protected paths changed without explicit allow:"
  echo "$CHANGED"
  exit 1
fi
```

---

## エージェントへの厳格要件（毎PR）

- **差分サマリ**（移動 / リネーム / 削除 / 追加）  
- **テスト全緑の証拠**（Pest / Vitest のログ要約）  
- **契約維持の証拠**（`route:list` スナップ比較、API外形テスト結果）  
- **影響範囲とリスク**（機能 / 性能 / セキュリティ）  
- **ロールバック手順**（対象コミット）  
- **保護ディレクトリ遵守の宣誓**（`./docs` `./infra` 無変更）  

---

## 禁止事項

- 失敗テスト / 解析エラー状態でのコミット  
- 巨大PR（±400行超は要分割）  
- 独自レイヤの新設（デフォルト準拠を崩す変更）  
- `./docs` `./infra` の変更（例外は `[ALLOW DOCS/INFRA]` 承認PRのみ）  

---

## 付録：最初の3本のPR（例）

### PR-1: 「CI/Contracts 基盤導入」
- `.github/workflows/ci.yml`（Protected guard + PHP + Contracts + Node）  
- `app/Console/Commands/ContractsRoutesSnapshotCommand.php`  
- `app/Console/Commands/ContractsRoutesCheckCommand.php`  
- `tests/Contracts/ApiContractTest.php`（最低1本）  
- 初回だけ `php artisan contracts:routes:snapshot` 実行  

---

### PR-2: 「Laravel標準ツリーの整備」
- 不足標準ファイルの `artisan make:*` 生成と配置確認  
- 既存の独自階層に手を出さず、まずは標準の空枠を作る  

---

### PR-3: 「ControllerのFormRequest化（画面A）」
- `app/Http/Requests/*` 新設・適用  
- ルート / API 外形の変化なし  
- テスト & スナップショットで証明  
