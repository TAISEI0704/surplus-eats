---
applyTo: "app/**/*.php"
---
# Security Best Practices (Backend)

Laravel バックエンドのセキュリティベストプラクティスです。

## 認証・認可

### 認証

#### 認証（本プロジェクト）

本プロジェクトでは **Laravel Jetstream + Fortify + Sanctum** を使用したマルチ認証システムを採用します。

**認証システムの構成:**
- **User 認証**: 一般ユーザー向け（デフォルトガード: `web`）
- **Seller 認証**: 販売者向け（カスタムガード: `sellers`）
- **セッションベース認証**: Cookie + Session での状態管理
- **API 認証**: Sanctum によるトークンベース認証（必要に応じて使用）

#### User 認証（Fortify デフォルト）

Fortify が提供する標準的な認証機能を使用します。

```php
// config/fortify.php
'guard' => 'web',
'username' => 'email',

// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

**User モデル:**
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
```

#### Seller 認証（カスタム実装）

本プロジェクトの要件に応じて、Seller 専用の認証を実装します。

**設定:**
```php
// config/auth.php
'defaults' => [
    'guard' => 'sellers',
    'passwords' => 'users',
],

'guards' => [
    'sellers' => [
        'driver' => 'session',
        'provider' => 'sellers',
    ],
],

'providers' => [
    'sellers' => [
        'driver' => 'eloquent',
        'model' => App\Models\Seller::class,
    ],
],
```

**✅ GOOD: シングルアクションコントローラで実装（アーキテクチャ準拠）**

```php
// app/Http/Controllers/Auth/Seller/RegisterController.php
namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\RegisterRequest;
use App\UseCases\Auth\Seller\RegisterUseCase;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    public function __construct(
        private RegisterUseCase $useCase
    ) {}

    public function __invoke(RegisterRequest $request): RedirectResponse
    {
        $seller = ($this->useCase)($request->validated());

        return redirect()->route('seller.dashboard');
    }
}

// app/Http/Controllers/Auth/Seller/LoginController.php
namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\LoginRequest;
use App\UseCases\Auth\Seller\LoginUseCase;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(
        private LoginUseCase $useCase
    ) {}

    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $seller = ($this->useCase)($request->validated());

        return redirect()->intended(route('seller.dashboard'));
    }
}

// app/Http/Controllers/Auth/Seller/LogoutController.php
namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\Seller\LogoutUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(
        private LogoutUseCase $useCase
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        ($this->useCase)();

        return redirect()->route('seller.login');
    }
}
```

**✅ GOOD: UseCase での認証ロジック実装**

```php
// app/UseCases/Auth/Seller/LoginUseCase.php
namespace App\UseCases\Auth\Seller;

use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUseCase
{
    /**
     * Seller ログイン処理
     *
     * @param array{email: string, password: string, remember?: bool} $data
     * @return Seller
     * @throws ValidationException
     */
    public function __invoke(array $data): Seller
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        // 認証試行
        if (!Auth::guard('sellers')->attempt($credentials, $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません'],
            ]);
        }

        // セッション再生成（セッション固定攻撃対策）
        request()->session()->regenerate();

        return Auth::guard('sellers')->user();
    }
}

// app/UseCases/Auth/Seller/RegisterUseCase.php
namespace App\UseCases\Auth\Seller;

use App\Models\Seller;
use App\Repositories\Auth\Seller\SellerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUseCase
{
    public function __construct(
        private SellerRepository $repository
    ) {}

    /**
     * Seller 登録処理
     *
     * @param array{name: string, email: string, password: string, phone?: string, address?: string} $data
     * @return Seller
     */
    public function __invoke(array $data): Seller
    {
        return DB::transaction(function () use ($data) {
            // パスワードハッシュ化
            $data['password'] = Hash::make($data['password']);

            // Seller作成
            $seller = $this->repository->create($data);

            // 自動ログイン
            Auth::guard('sellers')->login($seller);

            // セッション再生成
            request()->session()->regenerate();

            return $seller;
        });
    }
}

// app/UseCases/Auth/Seller/LogoutUseCase.php
namespace App\UseCases\Auth\Seller;

use Illuminate\Support\Facades\Auth;

class LogoutUseCase
{
    /**
     * Seller ログアウト処理
     */
    public function __invoke(): void
    {
        Auth::guard('sellers')->logout();

        // セッション無効化
        request()->session()->invalidate();

        // CSRF トークン再生成
        request()->session()->regenerateToken();
    }
}
```

**セキュリティ要件:**
- パスワードのハッシュ化（`Hash::make`）
- セッション再生成（セッション固定攻撃対策）
- CSRF トークン保護（標準で有効）
- ログイン試行の検証とエラーハンドリング
- remember_token の安全な管理
- 認証イベントのログ記録（推奨）

**❌ BAD: コントローラに直接ビジネスロジックを記述**

```php
// 認証ロジックをコントローラに直接記述するのは NG
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->password === $request->password) { // 平文比較 NG
            session(['user_id' => $user->id]);
            return redirect('/dashboard');
        }
    }
}
```

### 認可（Policy）

認可は **Policy** を使用して実装します。本プロジェクトでは、シングルアクションコントローラから Policy を呼び出します。

#### Policy 定義

```php
// app/Policies/ProductPolicy.php
namespace App\Policies;

use App\Models\{User, Product};

class ProductPolicy
{
    /**
     * 商品の閲覧権限
     */
    public function view(?User $user, Product $product): bool
    {
        // 公開商品 or 自分の商品
        return $product->is_public || ($user && $product->user_id === $user->id);
    }

    /**
     * 商品の更新権限
     */
    public function update(User $user, Product $product): bool
    {
        // 自分の商品のみ更新可能
        return $product->user_id === $user->id;
    }

    /**
     * 商品の削除権限
     */
    public function delete(User $user, Product $product): bool
    {
        // 自分の商品のみ削除可能
        return $product->user_id === $user->id;
    }
}

// app/Policies/SellerPolicy.php
namespace App\Policies;

use App\Models\{Seller, Product};

class SellerProductPolicy
{
    /**
     * Seller が商品を更新できるか
     */
    public function update(Seller $seller, Product $product): bool
    {
        return $product->seller_id === $seller->id;
    }

    /**
     * Seller が商品を削除できるか
     */
    public function delete(Seller $seller, Product $product): bool
    {
        return $product->seller_id === $seller->id;
    }
}
```

#### Controller での使用（アーキテクチャ準拠）

**✅ GOOD: シングルアクションコントローラで認可チェック**

```php
// app/Http/Controllers/Product/UpdateController.php
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\UseCases\Product\UpdateUseCase;
use App\Models\Product;

class UpdateController extends Controller
{
    public function __construct(
        private UpdateUseCase $useCase
    ) {}

    public function __invoke(int $id, UpdateRequest $request): ProductResource
    {
        // 商品を取得
        $product = Product::findOrFail($id);

        // Policy で認可チェック
        $this->authorize('update', $product);

        // UseCase 実行
        $updated = ($this->useCase)($id, $request->validated());

        return new ProductResource($updated);
    }
}

// app/Http/Controllers/Product/DeleteController.php
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\UseCases\Product\DeleteUseCase;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(
        private DeleteUseCase $useCase
    ) {}

    public function __invoke(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Policy で認可チェック
        $this->authorize('delete', $product);

        ($this->useCase)($id);

        return response()->json(['message' => '商品を削除しました'], 200);
    }
}
```

#### Seller ガードでの認可

Seller 認証の場合は、カスタムガードを使用します。

```php
// app/Http/Controllers/Seller/Product/UpdateController.php
namespace App\Http\Controllers\Seller\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\Product\UpdateRequest;
use App\UseCases\Seller\Product\UpdateUseCase;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    public function __construct(
        private UpdateUseCase $useCase
    ) {}

    public function __invoke(int $id, UpdateRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($id);

        // Seller ガードで認証されているか確認
        $seller = Auth::guard('sellers')->user();

        // Seller の商品かどうかチェック
        if ($product->seller_id !== $seller->id) {
            abort(403, 'この商品を編集する権限がありません');
        }

        ($this->useCase)($id, $request->validated());

        return redirect()->route('seller.products.show', $id)
            ->with('success', '商品を更新しました');
    }
}
```

#### Middleware での認可

ルート全体に認可を適用する場合は、Middleware を使用します。

```php
// routes/web.php
use App\Http\Controllers\Product;

Route::middleware(['auth:sellers'])->prefix('seller')->group(function () {
    Route::post('/products', Product\CreateController::class)->name('seller.products.store');
    Route::patch('/products/{id}', Product\UpdateController::class)->name('seller.products.update');
    Route::delete('/products/{id}', Product\DeleteController::class)->name('seller.products.destroy');
});

// カスタム Middleware
// app/Http/Middleware/EnsureSellerOwnsProduct.php
namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSellerOwnsProduct
{
    public function handle(Request $request, Closure $next)
    {
        $productId = $request->route('id');
        $product = Product::findOrFail($productId);

        $seller = Auth::guard('sellers')->user();

        if ($product->seller_id !== $seller->id) {
            abort(403, 'この商品にアクセスする権限がありません');
        }

        return $next($request);
    }
}
```

**❌ BAD: 認可チェックなし**

```php
// 認可チェックを省略すると、誰でも更新・削除できてしまう
class UpdateController extends Controller
{
    public function __invoke(int $id, Request $request)
    {
        $product = Product::findOrFail($id);

        // 認可チェックなし - 危険！
        $product->update($request->all());

        return redirect()->back();
    }
}
```

**❌ BAD: コントローラ内で手動チェック（Policy を使うべき）**

```php
class UpdateController extends Controller
{
    public function __invoke(int $id, Request $request)
    {
        $product = Product::findOrFail($id);

        // 手動チェックではなく Policy を使用すべき
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $product->update($request->validated());
        return redirect()->back();
    }
}
```

### Middleware による保護

本プロジェクトでは、User と Seller の2種類の認証ガードがあるため、ルート定義で適切な Middleware を指定します。

#### User 認証の保護

```php
// routes/web.php
use App\Http\Controllers\Product;
use App\Http\Controllers\Cart;
use App\Http\Controllers\Purchase;

// User 認証が必要なルート（web ガード）
Route::middleware(['auth:web', 'verified'])->group(function () {
    // 商品閲覧
    Route::get('/dashboard', Product\IndexController::class)->name('dashboard');
    Route::get('/products/{id}', Product\ShowController::class)->name('products.show');

    // カート
    Route::get('/cart', Cart\ShowController::class)->name('cart.show');
    Route::post('/cart/{productId}', Cart\AddController::class)->name('cart.add');
    Route::delete('/cart/{productId}', Cart\RemoveController::class)->name('cart.remove');

    // 購入
    Route::post('/purchase', Purchase\CreateController::class)->name('purchase.store');
    Route::get('/purchase/{id}', Purchase\ShowController::class)->name('purchase.show');
});
```

#### Seller 認証の保護

```php
// routes/web.php
use App\Http\Controllers\Seller;

// Seller 認証が必要なルート（sellers ガード）
Route::middleware(['auth:sellers'])->prefix('seller')->group(function () {
    // ダッシュボード
    Route::get('/dashboard', Seller\DashboardController::class)->name('seller.dashboard');

    // 商品管理
    Route::get('/products/create', Seller\Product\CreateController::class)->name('seller.products.create');
    Route::post('/products', Seller\Product\StoreController::class)->name('seller.products.store');
    Route::get('/products/{id}/edit', Seller\Product\EditController::class)->name('seller.products.edit');
    Route::patch('/products/{id}', Seller\Product\UpdateController::class)->name('seller.products.update');
    Route::delete('/products/{id}', Seller\Product\DeleteController::class)->name('seller.products.destroy');

    // プロフィール
    Route::get('/profile', Seller\ProfileController::class)->name('seller.profile.show');
});
```

#### カスタム Middleware の実装

```php
// app/Http/Middleware/EnsureUserHasActiveSubscription.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasActiveSubscription
{
    /**
     * サブスクリプションが有効かチェック
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasActiveSubscription()) {
            return redirect()->route('billing')
                ->with('error', 'サブスクリプションの登録が必要です');
        }

        return $next($request);
    }
}

// app/Http/Middleware/EnsureSellerIsVerified.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerIsVerified
{
    /**
     * Seller が認証済みかチェック
     */
    public function handle(Request $request, Closure $next): Response
    {
        $seller = Auth::guard('sellers')->user();

        if (!$seller || !$seller->hasVerifiedEmail()) {
            return redirect()->route('seller.verification.notice')
                ->with('error', 'メールアドレスの確認が必要です');
        }

        return $next($request);
    }
}
```

#### Middleware の登録

```php
// bootstrap/app.php または app/Http/Kernel.php
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'subscription' => \App\Http\Middleware\EnsureUserHasActiveSubscription::class,
    'seller.verified' => \App\Http\Middleware\EnsureSellerIsVerified::class,
];
```

#### Middleware の使用例

```php
// サブスクリプション必須の機能
Route::middleware(['auth:web', 'subscription'])->group(function () {
    Route::get('/premium/features', PremiumFeatureController::class);
});

// 認証済み Seller のみアクセス可能
Route::middleware(['auth:sellers', 'seller.verified'])->group(function () {
    Route::post('/seller/products', Seller\Product\StoreController::class);
});
```

---

## CSRF対策

### Blade/Inertia での CSRF トークン

```php
// Blade
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <!-- フォームフィールド -->
</form>

// Inertia（自動で CSRF トークンが含まれる）
import { useForm } from '@inertiajs/react';

const { post } = useForm({ name: '', price: 0 });

post(route('products.store')); // CSRFトークンは自動的に含まれる
```

### API での CSRF 対策

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),

// Axios設定（resources/js/bootstrap.js）
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
```

---

## SQLインジェクション対策

```php
// ✅ GOOD: パラメータバインディング
$products = DB::table('products')
    ->where('category', $category)
    ->where('price', '>=', $minPrice)
    ->get();

// Eloquent（自動的に安全）
$products = Product::where('category', $category)
    ->where('price', '>=', $minPrice)
    ->get();

// ❌ BAD: 生のSQL文字列結合
$products = DB::select(
    "SELECT * FROM products WHERE category = '$category' AND price >= $minPrice"
);

// ❌ BAD: whereRaw での文字列結合
$products = Product::whereRaw("category = '$category'")->get();

// ✅ GOOD: whereRaw でもバインディング使用
$products = Product::whereRaw('category = ?', [$category])->get();
```

---

## Mass Assignment 対策

```php
// Model での fillable/guarded 設定
class Product extends Model
{
    // ✅ GOOD: fillable で許可するフィールドを明示
    protected $fillable = [
        'name',
        'price',
        'description',
        'category_id',
    ];

    // または guarded で保護するフィールドを明示
    protected $guarded = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}

// Controller
class ProductController extends Controller
{
    // ✅ GOOD: FormRequest でバリデーション済みのデータのみ使用
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
    }

    // ❌ BAD: リクエスト全体をそのまま使用
    public function store(Request $request)
    {
        $product = Product::create($request->all()); // user_id等が上書きされる危険
    }
}

// ✅ GOOD: Repository層での実装（CQRS パターン）
class DashboardRepository
{
    // 明示的にフィールドを指定して Mass Assignment を防ぐ
    public function create(array $data): Dashboard
    {
        return Dashboard::create([
            'name' => $data['name'],
            'layout' => $data['layout'],
            'user_id' => $data['user_id'],
            'is_public' => $data['is_public'] ?? false,
        ]);
    }

    public function update(Dashboard $dashboard, array $data): Dashboard
    {
        $dashboard->update([
            'name' => $data['name'] ?? $dashboard->name,
            'layout' => $data['layout'] ?? $dashboard->layout,
            'is_public' => $data['is_public'] ?? $dashboard->is_public,
            // user_id は更新しない（セキュリティ）
        ]);

        return $dashboard->fresh();
    }
}
```

---

## パスワードセキュリティ

### パスワードハッシュ化

```php
use Illuminate\Support\Facades\Hash;

// ✅ GOOD: bcrypt でハッシュ化
$user->password = Hash::make($request->password);

// パスワード検証
if (Hash::check($request->password, $user->password)) {
    // 認証成功
}

// ❌ BAD: 平文保存
$user->password = $request->password;
```

### パスワードポリシー

```php
// FormRequest
public function rules(): array
{
    return [
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ],
    ];
}
```

---

## ファイルアップロードセキュリティ

```php
// ✅ GOOD: バリデーションと安全なファイル名
public function upload(Request $request): JsonResponse
{
    $request->validate([
        'image' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
    ]);

    $file = $request->file('image');

    // 安全なファイル名生成
    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

    // storage/app/public/images に保存
    $path = $file->storeAs('images', $filename, 'public');

    return response()->json(['path' => $path]);
}

// ❌ BAD: バリデーションなし、元のファイル名使用
public function upload(Request $request)
{
    $file = $request->file('image');
    $path = $file->store('images'); // 拡張子チェックなし

    return response()->json(['path' => $path]);
}
```

### ファイルタイプ検証

```php
use Illuminate\Validation\Rules\File;

public function rules(): array
{
    return [
        'document' => [
            'required',
            File::types(['pdf', 'doc', 'docx'])
                ->max(10 * 1024), // 10MB
        ],
        'image' => [
            'required',
            File::image()
                ->min(100) // 100KB
                ->max(5 * 1024) // 5MB
                ->dimensions(Rule::dimensions()->maxWidth(4000)->maxHeight(4000)),
        ],
    ];
}
```

---

## 環境変数と秘密情報

```php
// ✅ GOOD: 秘密情報は .env に
// .env
STRIPE_SECRET=sk_live_xxxxx
AWS_ACCESS_KEY_ID=xxxxx
AWS_SECRET_ACCESS_KEY=xxxxx

// config/services.php
'stripe' => [
    'secret' => env('STRIPE_SECRET'),
],

// 使用
$secret = config('services.stripe.secret');

// ❌ BAD: ハードコード
class PaymentService
{
    private $stripeSecret = 'sk_live_xxxxx'; // 絶対にNG
}

// .env.example（秘密情報は含めない）
STRIPE_SECRET=
AWS_ACCESS_KEY_ID=
```

### .gitignore 設定

```gitignore
.env
.env.backup
.env.production
.phpunit.result.cache
node_modules/
vendor/
storage/*.key
.DS_Store
```

---

## レート制限

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

// カスタムレート制限
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// プレミアムユーザーには高い制限
RateLimiter::for('uploads', function (Request $request) {
    return $request->user()->isPremium()
        ? Limit::none()
        : Limit::perMinute(10)->by($request->user()->id);
});
```

---

## ログとモニタリング

```php
// ✅ GOOD: セキュリティイベントのログ
use Illuminate\Support\Facades\Log;

// ログイン試行
Log::channel('security')->info('ログイン試行', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);

// ログイン失敗
Log::channel('security')->warning('ログイン失敗', [
    'email' => $request->email,
    'ip' => $request->ip(),
]);

// 認可エラー
Log::channel('security')->warning('認可エラー', [
    'user_id' => auth()->id(),
    'action' => 'update',
    'resource' => 'Product',
    'resource_id' => $product->id,
]);

// ❌ BAD: 機密情報をログに含める
Log::info('パスワード変更', [
    'user_id' => $user->id,
    'new_password' => $request->password, // NG
]);
```

---

## HTTPS強制

```php
// App\Providers\AppServiceProvider
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}

// Middleware
namespace App\Http\Middleware;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
```

---

## セキュリティヘッダー

```php
// config/cors.php
return [
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

// Middleware でセキュリティヘッダー追加
namespace App\Http\Middleware;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');

        return $response;
    }
}
```

---

## 定期的なセキュリティチェック

```bash
# 依存関係の脆弱性チェック
composer audit

# 静的解析
./vendor/bin/phpstan analyse

# セキュリティテスト
./vendor/bin/pest --group=security
```
