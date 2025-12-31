---
applyTo: "app/Http/Resources/**/*.php,tests/Unit/Http/Resources/**/*.php"
---
# Resource Layer Rules

Resource層（API Resource）はデータの整形とレスポンスフォーマットを担当し、フロントエンドに最適な形でデータを提供します。

---

## 責務

### ✅ Resourceが行うべきこと

- Modelデータの整形・変換
- 不要な情報の除外
- リレーションデータの整形
- 日付フォーマットの統一
- 条件付きデータの出力
- TypeScript型定義との整合性確保

### ❌ Resourceが行ってはいけないこと

- ビジネスロジック
- DB操作
- 外部API呼び出し
- 複雑な計算処理（ModelまたはServiceへ）

---

## 命名規則

```
app/Http/Resources/{Feature}/{Entity}Resource.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Entity}`: エンティティ名（例: `Dashboard`, `Device`, `User`）

### 具体例

```
app/Http/Resources/
├── Dashboard/
│   ├── DashboardResource.php        # ダッシュボード
│   └── DashboardCollection.php      # カスタムメタデータが必要な場合
├── Device/
│   ├── DeviceResource.php
│   └── DeviceCollection.php
├── Karte/
│   └── KarteResource.php
└── User/
    ├── UserResource.php
    └── ProfileResource.php          # プロフィール
```

### Collectionクラス名（カスタムメタデータが必要な場合）

```
app/Http/Resources/{Feature}/{Entity}Collection.php
```

例: `Dashboard/DashboardCollection.php`

---

## 基本実装パターン

### 基本的なResource実装

```php
<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * リソースを配列に変換
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'layout' => $this->layout,
            'isPublic' => $this->is_public, // camelCase変換
            'userId' => $this->user_id,
            'settings' => $this->settings,
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
```

### リレーションの条件付き読み込み

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'price' => $this->price,

        // リレーションが読み込まれている場合のみ含める
        'category' => $this->whenLoaded('category', function () {
            return new CategoryResource($this->category);
        }),

        // または省略記法
        'category' => CategoryResource::make($this->whenLoaded('category')),

        // 複数のリレーション
        'tags' => TagResource::collection($this->whenLoaded('tags')),

        // ピボットテーブルの情報を含める
        'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
    ];
}
```

### 条件付きデータの出力

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,

        // 条件に応じて属性を含める
        'email' => $this->when(
            $request->user()?->can('view', $this->resource),
            $this->email
        ),

        // 管理者のみに表示
        'secret' => $this->when(
            $request->user()?->isAdmin(),
            'secret-value'
        ),

        // nullでない場合のみ含める
        'description' => $this->when($this->description !== null, $this->description),

        // または
        'deletedAt' => $this->when(
            !is_null($this->deleted_at),
            $this->deleted_at->toIso8601String()
        ),
    ];
}
```

### マージ可能な条件付きデータ

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,

        // 条件に応じて複数の属性をマージ
        $this->mergeWhen($request->user()?->isAdmin(), [
            'internalNotes' => $this->internal_notes,
            'costPrice' => $this->cost_price,
            'profit' => $this->price - $this->cost_price,
        ]),

        // 認証ユーザーのみに表示
        $this->mergeWhen(auth()->check(), [
            'canEdit' => $request->user()->can('update', $this->resource),
            'canDelete' => $request->user()->can('delete', $this->resource),
        ]),
    ];
}
```

### カスタムCollection実装

```php
<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DashboardCollection extends ResourceCollection
{
    /**
     * リソースコレクションを配列に変換
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'publicCount' => $this->collection->filter(
                    fn($dashboard) => $dashboard->is_public
                )->count(),
            ],
        ];
    }

    /**
     * トップレベルのメタデータを追加
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('/api'),
        ];
    }
}
```

### ページネーション対応

```php
// Controllerでの使用例
namespace App\Http\Controllers\Dashboard;

public function __invoke(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
{
    $dashboards = $this->query->paginate(15);

    return DashboardResource::collection($dashboards);
    // ページネーション情報は自動的に含まれる
}

// カスタムページネーションメタデータ
public function with(Request $request): array
{
    return [
        'meta' => [
            'key' => 'value',
        ],
    ];
}
```

### ネストしたリソース

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,

        // ネストしたリソース
        'category' => new CategoryResource($this->whenLoaded('category')),

        // ネストしたリソースコレクション
        'images' => ImageResource::collection($this->whenLoaded('images')),

        // 深くネストしたリソース
        'author' => new UserResource($this->whenLoaded('author', function () {
            return $this->author->load('profile');
        })),
    ];
}
```

### 追加メタデータ

```php
// Controllerでの使用
namespace App\Http\Controllers\Dashboard;

public function __invoke(int $id): DashboardResource
{
    $dashboard = $this->query->findById($id);

    return (new DashboardResource($dashboard))
        ->additional([
            'meta' => [
                'viewCount' => $dashboard->view_count,
                'isPopular' => $dashboard->view_count > 1000,
            ],
        ]);
}
```

### レスポンスのカスタマイズ

```php
use Illuminate\Http\JsonResponse;

/**
 * レスポンスをカスタマイズ
 *
 * @param Request $request
 * @param JsonResponse $response
 * @return void
 */
public function withResponse(Request $request, JsonResponse $response): void
{
    $response->header('X-Resource-Version', '1.0');
    $response->setStatusCode(200);
}
```

### TypeScript型定義との連携例

```php
/**
 * Dashboard型に対応するResourceの実装
 *
 * TypeScript:
 * interface Dashboard {
 *   id: number;
 *   name: string;
 *   description: string | null;
 *   layout: 'grid' | 'list' | 'kanban';
 *   isPublic: boolean;
 *   userId: number;
 *   user: User;
 *   createdAt: string; // ISO8601
 * }
 */
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'description' => $this->description,
        'layout' => $this->layout,
        'isPublic' => $this->is_public,
        'userId' => $this->user_id,
        'user' => new UserResource($this->whenLoaded('user')),
        'createdAt' => $this->created_at->toIso8601String(),
    ];
}
```

### 計算プロパティの追加

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'price' => $this->price,
        'discount' => $this->discount,

        // 計算プロパティ
        'finalPrice' => $this->price - $this->discount,
        'discountRate' => $this->discount > 0
            ? round(($this->discount / $this->price) * 100, 2)
            : 0,

        // フォーマット済みの値
        'priceFormatted' => '¥' . number_format($this->price),

        // 日付の相対表現
        'createdAtHuman' => $this->created_at->diffForHumans(),
    ];
}
```

---

## チェックリスト

- [ ] 命名規約 `{Feature}/{Entity}Resource.php` に従っているか
- [ ] snake_caseからcamelCaseへの変換が適切に行われているか
- [ ] 不要な情報（パスワード等）が除外されているか
- [ ] 日付フォーマットが統一されているか（ISO8601推奨）
- [ ] リレーションは`whenLoaded`で条件付き読み込みされているか
- [ ] TypeScript型定義と整合性が取れているか
- [ ] 条件付き属性は適切に実装されているか
- [ ] コレクションにメタデータが適切に含まれているか
- [ ] N+1問題を引き起こさないか
- [ ] 権限による表示制御が適切に実装されているか
- [ ] テストが書かれているか
