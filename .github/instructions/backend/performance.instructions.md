---
applyTo: "app/**/*.php"
---
# Performance Optimization

パフォーマンス最適化のベストプラクティスです。

## データベース最適化

### N+1問題の回避

```php
// ❌ BAD: N+1問題
$products = Product::all();

foreach ($products as $product) {
    echo $product->category->name; // クエリが N 回実行される
    foreach ($product->tags as $tag) { // さらに N 回
        echo $tag->name;
    }
}

// ✅ GOOD: Eager Loading
$products = Product::with(['category', 'tags'])->get();

foreach ($products as $product) {
    echo $product->category->name; // すでにロード済み
    foreach ($product->tags as $tag) {
        echo $tag->name;
    }
}

// ✅ GOOD: ネストした関連のロード
$products = Product::with(['category', 'tags', 'reviews.user'])->get();

// ✅ GOOD: 特定のカラムのみロード
$products = Product::with([
    'category:id,name',
    'tags:id,name',
])->get();

// ✅ GOOD: Query層での実装（CQRS パターン）
class DashboardQuery
{
    public function findAllWithUser(): Collection
    {
        // Eager Loadingで N+1 問題を回避
        return Dashboard::with('user')->get();
    }

    public function findAllWithRelations(): Collection
    {
        // 複数のリレーションを一度にロード
        return Dashboard::with(['user', 'widgets', 'shares'])
            ->get();
    }
}
```

### クエリの最適化

```php
// ❌ BAD: 全カラム取得
$products = Product::all();

// ✅ GOOD: 必要なカラムのみ取得
$products = Product::select('id', 'name', 'price')->get();

// ❌ BAD: count() で全レコード取得
$count = Product::all()->count();

// ✅ GOOD: DB レベルで COUNT
$count = Product::count();

// ✅ GOOD: exists() で存在チェック
if (Product::where('slug', $slug)->exists()) {
    // ...
}

// ❌ BAD: チャンク処理なしで大量データ
$products = Product::all(); // メモリ不足の可能性

// ✅ GOOD: チャンク処理
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // 処理
    }
});

// ✅ GOOD: lazyById (大量データ処理)
Product::lazyById(100)->each(function ($product) {
    // メモリ効率的な処理
});
```

### インデックス

```php
// マイグレーション
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique(); // ユニークインデックス
        $table->decimal('price', 10, 2);
        $table->foreignId('category_id')->constrained(); // 外部キー + インデックス
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        
        // 複合インデックス
        $table->index(['category_id', 'is_active']);
        
        // 全文検索インデックス
        $table->fullText(['name', 'description']);
    });
}

// 既存テーブルにインデックス追加
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->index('slug');
        $table->index(['category_id', 'created_at']);
    });
}
```

---

## キャッシュ戦略

### クエリ結果のキャッシュ

```php
use Illuminate\Support\Facades\Cache;

// ✅ GOOD: remember でキャッシュ
public function getCategories(): Collection
{
    return Cache::remember('categories.all', 3600, function () {
        return Category::orderBy('name')->get();
    });
}

// ✅ GOOD: タグ付きキャッシュ
public function getFeaturedProducts(): Collection
{
    return Cache::tags(['products', 'featured'])
        ->remember('products.featured', 3600, function () {
            return Product::where('is_featured', true)
                ->with('category')
                ->get();
        });
}

// ✅ GOOD: Query層でのキャッシュ実装（CQRS パターン）
class DashboardQuery
{
    public function findAll(): Collection
    {
        return Cache::remember('dashboards.all', 3600, function () {
            return Dashboard::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findPublic(): Collection
    {
        return Cache::tags(['dashboards', 'public'])
            ->remember('dashboards.public', 3600, function () {
                return Dashboard::where('is_public', true)
                    ->with('user')
                    ->get();
            });
    }
}

// キャッシュ無効化
public function clearProductCache(): void
{
    Cache::tags(['products'])->flush();
}

// 特定キーの削除
Cache::forget('categories.all');
```

### モデルキャッシュ

```php
// Model
class Product extends Model
{
    protected static function booted(): void
    {
        // 作成/更新時にキャッシュクリア
        static::saved(function () {
            Cache::tags(['products'])->flush();
        });
        
        static::deleted(function () {
            Cache::tags(['products'])->flush();
        });
    }
}
```

### Redis キャッシュ

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),

// .env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

// セッションもRedisに
SESSION_DRIVER=redis

// キュー もRedisに
QUEUE_CONNECTION=redis
```

---

## コレクション最適化

```php
use Illuminate\Support\Collection;

// ❌ BAD: foreachで手動処理
$total = 0;
foreach ($products as $product) {
    $total += $product->price;
}

// ✅ GOOD: コレクションメソッド
$total = $products->sum('price');

// ❌ BAD: 複数回ループ
$expensive = [];
foreach ($products as $product) {
    if ($product->price > 10000) {
        $expensive[] = $product;
    }
}

$names = [];
foreach ($expensive as $product) {
    $names[] = $product->name;
}

// ✅ GOOD: メソッドチェーン
$names = $products
    ->filter(fn($p) => $p->price > 10000)
    ->pluck('name');

// ✅ GOOD: lazy() で大量データを効率的に処理
$products->lazy()
    ->filter(fn($p) => $p->price > 1000)
    ->map(fn($p) => $p->name)
    ->take(100)
    ->all();
```

---

### 画像最適化

```php
// 画像リサイズ・圧縮
use Intervention\Image\Facades\Image;

public function upload(Request $request): JsonResponse
{
    $file = $request->file('image');
    
    // オリジナル
    $path = $file->store('images/original', 'public');
    
    // サムネイル生成
    $image = Image::make($file);
    $image->fit(300, 300);
    $thumbnailPath = 'images/thumbnails/' . basename($path);
    $image->save(storage_path('app/public/' . $thumbnailPath));
    
    // WebP変換
    $image = Image::make($file);
    $webpPath = 'images/webp/' . pathinfo($path, PATHINFO_FILENAME) . '.webp';
    $image->encode('webp', 80);
    $image->save(storage_path('app/public/' . $webpPath));
    
    return response()->json([
        'original' => $path,
        'thumbnail' => $thumbnailPath,
        'webp' => $webpPath,
    ]);
}
```

---

## ペジネーション

```php
// ✅ GOOD: ページネーション
$products = Product::paginate(20);

return Inertia::render('Products/Index', [
    'products' => $products,
]);

// ✅ GOOD: カーソルページネーション（大規模データ）
$products = Product::cursorPaginate(20);

// ✅ GOOD: シンプルページネーション（前後のみ）
$products = Product::simplePaginate(20);
```

---

## バックグラウンドジョブ

```php
// ❌ BAD: 重い処理を同期実行
public function store(Request $request)
{
    $product = Product::create($request->all());
    
    // メール送信（遅い）
    Mail::to('admin@example.com')->send(new ProductCreated($product));
    
    // 画像処理（遅い）
    $this->processImages($product);
    
    return redirect()->route('products.show', $product);
}

// ✅ GOOD: ジョブキューで非同期実行
public function store(Request $request)
{
    $product = Product::create($request->all());
    
    // 非同期で実行
    ProcessProductImages::dispatch($product);
    SendProductNotification::dispatch($product);
    
    return redirect()->route('products.show', $product);
}
```
