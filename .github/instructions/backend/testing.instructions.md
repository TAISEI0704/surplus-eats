---
applyTo: "tests/**/*.php"
---
# Testing Standards

Laravel バックエンドのテスト戦略と実装パターンです。

## PHPUnit（PHP）

### Feature Test

```php
<?php

namespace Tests\Feature;

use App\Models\{User, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品一覧を取得できる()
    {
        Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertOk()
            ->assertInertia(fn($page) => $page
                ->component('Products/Index')
                ->has('products', 3)
            );
    }

    /** @test */
    public function 商品を作成できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'テスト商品',
                'price' => 1000,
                'description' => '説明文',
            ]);

        $response->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'テスト商品',
            'price' => 1000,
        ]);
    }

    /** @test */
    public function バリデーションエラーで失敗する()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => '', // 必須
                'price' => -100, // 正の数
            ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    /** @test */
    public function 認証されていないと作成できない()
    {
        $response = $this->post(route('products.store'), [
            'name' => 'テスト商品',
            'price' => 1000,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 権限がないと削除できない()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->delete(route('products.destroy', $product));

        $response->assertForbidden();
    }
}
```

### Unit Test（UseCase）

```php
<?php

namespace Tests\Unit\UseCases;

use App\Http\Resources\ProductResource;
use App\UseCases\Product\CreateUseCase;
use App\Repositories\Product\ProductRepository;
use App\Services\NotificationService;
use App\Models\Product;
use Tests\TestCase;
use Mockery;

class CreateProductUseCaseTest extends TestCase
{
    /** @test */
    public function 商品を正しく作成する()
    {
        $repository = Mockery::mock(ProductRepository::class);
        $notificationService = Mockery::mock(NotificationService::class);

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn(new Product([
                'id' => 1,
                'name' => 'テスト商品',
                'price' => 1000,
            ]));

        $notificationService->shouldReceive('sendProductCreated')
            ->once()
            ->with(Mockery::type(Product::class));

        $useCase = new CreateUseCase($repository, $notificationService);

        $result = ($useCase)([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => null,
        ]);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('テスト商品', $result->name);
    }

    /** @test */
    public function 価格が負の場合は例外をスローする()
    {
        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('価格は0以上である必要があります');

        $repository = Mockery::mock(ProductRepository::class);
        $notificationService = Mockery::mock(NotificationService::class);

        $useCase = new CreateUseCase($repository, $notificationService);

        ($useCase)([
            'name' => 'テスト商品',
            'price' => -100,
            'description' => null,
        ]);
    }
}
```

### Unit Test（Query）- CQRS パターン

```php
<?php

namespace Tests\Unit\Queries\Dashboard;

use App\Queries\Dashboard\DashboardQuery;
use App\Models\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardQueryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function IDでダッシュボードを取得できる()
    {
        $dashboard = Dashboard::factory()->create();
        $query = new DashboardQuery();

        $result = $query->findById($dashboard->id);

        $this->assertNotNull($result);
        $this->assertEquals($dashboard->id, $result->id);
    }

    /** @test */
    public function ユーザーIDでダッシュボードを取得できる()
    {
        $userId = 1;
        Dashboard::factory()->count(2)->create(['user_id' => $userId]);
        Dashboard::factory()->create(['user_id' => 2]);

        $query = new DashboardQuery();
        $results = $query->findByUserId($userId);

        $this->assertCount(2, $results);
    }

    /** @test */
    public function リレーションを含めて取得できる()
    {
        $dashboard = Dashboard::factory()->create();
        $query = new DashboardQuery();

        $result = $query->findByIdWithUser($dashboard->id);

        $this->assertTrue($result->relationLoaded('user'));
    }
}
```

### Unit Test（Repository）- CQRS パターン

```php
<?php

namespace Tests\Unit\Repositories\Dashboard;

use App\Repositories\Dashboard\DashboardRepository;
use App\Models\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ダッシュボードを作成できる()
    {
        $repository = new DashboardRepository();
        $data = [
            'name' => 'テストダッシュボード',
            'layout' => 'grid',
            'user_id' => 1,
            'is_public' => true,
        ];

        $result = $repository->create($data);

        $this->assertInstanceOf(Dashboard::class, $result);
        $this->assertEquals('テストダッシュボード', $result->name);
    }

    /** @test */
    public function ダッシュボードを更新できる()
    {
        $dashboard = Dashboard::factory()->create(['name' => '元の名前']);
        $repository = new DashboardRepository();

        $updated = $repository->update($dashboard, ['name' => '新しい名前']);

        $this->assertEquals('新しい名前', $updated->name);
    }

    /** @test */
    public function ダッシュボードを削除できる()
    {
        $dashboard = Dashboard::factory()->create();
        $repository = new DashboardRepository();

        $result = $repository->delete($dashboard);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('dashboards', ['id' => $dashboard->id]);
    }
}
```

---

## テストのベストプラクティス

### AAA（Arrange-Act-Assert）パターン

```php
/** @test */
public function 商品を作成できる()
{
    // Arrange（準備）
    $user = User::factory()->create();
    $data = [
        'name' => 'テスト商品',
        'price' => 1000,
    ];

    // Act（実行）
    $response = $this->actingAs($user)->post(route('products.store'), $data);

    // Assert（検証）
    $response->assertRedirect();
    $this->assertDatabaseHas('products', $data);
}
```

### テストデータの作成

```php
// Factory使用
Product::factory()->create(['price' => 1000]);
Product::factory()->count(5)->create();
Product::factory()->expensive()->create(); // カスタム状態

// Seeder使用（大量データが必要な場合）
$this->seed(ProductSeeder::class);
```

### モック/スタブ

```php
// モック（振る舞いを検証）
$service = Mockery::mock(NotificationService::class);
$service->shouldReceive('send')
    ->once()
    ->with(Mockery::type(Product::class))
    ->andReturn(true);

// スタブ（戻り値を返すだけ）
$repository = Mockery::mock(ProductRepository::class);
$repository->shouldReceive('findById')
    ->andReturn(new Product(['id' => 1]));
```

### データベーステスト

```php
class DatabaseOperationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function トランザクションがロールバックされる()
    {
        // テスト内での変更は自動的にロールバックされる
        $product = Product::factory()->create(['name' => 'テスト']);

        $this->assertDatabaseHas('products', ['name' => 'テスト']);
    }

    /** @test */
    public function 外部キー制約が機能する()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // カテゴリ削除時に商品も削除される（CASCADE設定時）
        $category->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
```

### Policy テスト

```php
class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 所有者は商品を更新できる()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('update', $product));
    }

    /** @test */
    public function 他のユーザーは商品を更新できない()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $otherUser->id]);

        $this->assertFalse($user->can('update', $product));
    }
}
```

### API テスト

```php
class ApiEndpointTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品一覧をJSONで取得できる()
    {
        Product::factory()->count(3)->create();

        $response = $this->get('/api/products');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price'],
                ],
            ]);
    }

    /** @test */
    public function 認証なしでは401を返す()
    {
        $response = $this->get('/api/products');

        $response->assertUnauthorized();
    }
}
```

### カバレッジ目標

- **Feature Test**: 主要なユーザーフローを網羅
- **Unit Test**: ビジネスロジック（UseCase/Service）は100%を目指す
- **Repository Test**: データアクセスロジックのカバレッジ80%以上
