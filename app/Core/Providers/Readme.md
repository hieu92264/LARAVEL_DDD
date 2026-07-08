# Core Providers

Thư mục `Providers` chứa các service provider của module `Core`.

Service provider là nơi đăng ký dependency vào Laravel Service Container. Nói ngắn gọn, đây là nơi khai báo:

- Interface nào sẽ dùng implementation nào.
- Object nào nên được tạo mới mỗi lần resolve.
- Object nào nên được dùng lại trong suốt vòng đời application/request.
- Những cấu hình khởi động nào cần chạy khi application boot.

Trong module này, file chính hiện tại là:

```text
CoreServiceProvider.php
```

## Vai trò của `CoreServiceProvider`

`CoreServiceProvider` đang bind các contract ở tầng `Application` với implementation ở tầng `Infrastructure`.

Ví dụ hiện tại:

```php
$this->app->singleton(PasswordHasher::class, LaravelPasswordHasher::class);

$this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
```

Ý nghĩa:

- Khi code cần `PasswordHasher`, Laravel sẽ cung cấp `LaravelPasswordHasher`.
- Khi code cần `TransactionManager`, Laravel sẽ cung cấp `LaravelTransactionManager`.
- Application layer chỉ phụ thuộc vào interface, không phụ thuộc trực tiếp vào class hạ tầng.

Đây là cách giữ hướng phụ thuộc đúng trong DDD/Clean Architecture:

```text
Application contract <- Infrastructure implementation
```

## `bind()`

`bind()` đăng ký một dependency theo kiểu tạo instance mới mỗi lần container resolve.

Ví dụ:

```php
$this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
```

Khi một class cần `TransactionManager`:

```php
use App\Core\Application\Contracts\TransactionManager;

final class CreateOrderHandler
{
    public function __construct(
        private readonly TransactionManager $transactionManager,
    ) {
    }
}
```

Laravel sẽ tự tạo `LaravelTransactionManager` và inject vào constructor.

### Khi nào nên dùng `bind()`

Nên dùng `bind()` khi:

- Object nhẹ, tạo mới không tốn nhiều chi phí.
- Object không cần dùng lại cùng một instance.
- Object có thể chứa trạng thái tạm thời trong quá trình xử lý.
- Mỗi lần resolve nên là một instance độc lập.
- Implementation phụ thuộc vào context runtime hoặc dependency khác.

Ví dụ phù hợp trong project:

```php
$this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
```

`LaravelTransactionManager` chỉ là adapter gọi `DB::transaction()`. Nó không cần giữ state dùng chung lâu dài, nên `bind()` là lựa chọn rõ ràng và an toàn.

## `singleton()`

`singleton()` đăng ký một dependency theo kiểu chỉ tạo một instance duy nhất. Sau lần resolve đầu tiên, các lần resolve sau sẽ dùng lại chính instance đó.

Ví dụ:

```php
$this->app->singleton(PasswordHasher::class, LaravelPasswordHasher::class);
```

Khi nhiều class khác nhau cần `PasswordHasher`, Laravel vẫn trả về cùng một instance của `LaravelPasswordHasher`.

### Khi nào nên dùng `singleton()`

Nên dùng `singleton()` khi:

- Object stateless, không giữ dữ liệu thay đổi theo request hoặc user.
- Object tạo ra tốn chi phí và có thể dùng lại an toàn.
- Object đại diện cho một service dùng chung.
- Object không chứa dữ liệu nghiệp vụ tạm thời.
- Object không phụ thuộc vào request hiện tại, authenticated user hiện tại hoặc dữ liệu runtime dễ thay đổi.

Ví dụ phù hợp trong project:

```php
$this->app->singleton(PasswordHasher::class, LaravelPasswordHasher::class);
```

`LaravelPasswordHasher` là service băm và kiểm tra mật khẩu. Nếu class này không lưu trạng thái theo user/request, nó có thể được dùng lại như một singleton.

### Lưu ý khi dùng `singleton()`

Không nên dùng `singleton()` cho object có state thay đổi.

Ví dụ không nên:

```php
final class CurrentUserContext
{
    public function __construct(
        public ?int $userId = null,
    ) {
    }
}
```

Nếu class trên được đăng ký bằng `singleton()`, dữ liệu user có thể bị giữ lại ngoài mong muốn trong các môi trường chạy lâu như queue worker, Octane hoặc long-running process.

## `scoped()`

`scoped()` cũng dùng lại instance, nhưng chỉ trong một lifecycle nhất định của Laravel, thường là một request hoặc một job.

Ví dụ:

```php
$this->app->scoped(RequestContext::class, function () {
    return new RequestContext();
});
```

### Khi nào nên dùng `scoped()`

Nên dùng `scoped()` khi:

- Object cần dùng lại trong cùng một request/job.
- Object có state gắn với request/job hiện tại.
- Không muốn state bị giữ lại sang request/job tiếp theo.
- Project chạy trong môi trường long-running như Laravel Octane hoặc queue worker.

Ví dụ phù hợp:

```php
$this->app->scoped(CurrentUserContext::class, function () {
    return new CurrentUserContext();
});
```

Trong một request, nhiều service có thể dùng chung `CurrentUserContext`. Sang request khác, Laravel sẽ tạo context mới.

## `instance()`

`instance()` đưa một object đã được tạo sẵn vào container.

Ví dụ:

```php
$config = new PaymentGatewayConfig(
    apiKey: config('services.payment.api_key'),
);

$this->app->instance(PaymentGatewayConfig::class, $config);
```

### Khi nào nên dùng `instance()`

Nên dùng `instance()` khi:

- Object đã được tạo sẵn trước đó.
- Muốn container luôn trả về đúng object đó.
- Object là cấu hình bất biến hoặc value object dùng chung.

Không nên lạm dụng `instance()` cho service có logic phức tạp, vì nó làm lifecycle của object kém rõ ràng hơn so với `bind()` hoặc `singleton()`.

## Binding bằng closure

Khi implementation cần cấu hình hoặc logic khởi tạo riêng, có thể bind bằng closure.

Ví dụ:

```php
$this->app->bind(TransactionManager::class, function ($app) {
    return new LaravelTransactionManager();
});
```

Closure hữu ích khi:

- Constructor cần tham số không tự resolve được.
- Cần đọc config.
- Cần chọn implementation theo môi trường.
- Cần setup object trước khi trả về.

Ví dụ chọn implementation theo config:

```php
$this->app->bind(PaymentGateway::class, function () {
    return match (config('payment.driver')) {
        'stripe' => new StripePaymentGateway(),
        'paypal' => new PaypalPaymentGateway(),
        default => throw new InvalidArgumentException('Unsupported payment driver.'),
    };
});
```

## So sánh nhanh

| Kiểu đăng ký | Cách hoạt động | Nên dùng khi |
| --- | --- | --- |
| `bind()` | Tạo instance mới mỗi lần resolve | Service nhẹ, không cần dùng lại instance |
| `singleton()` | Tạo một lần, dùng lại mãi | Service stateless, dùng chung an toàn |
| `scoped()` | Dùng lại trong cùng request/job | State gắn với request/job |
| `instance()` | Dùng object đã tạo sẵn | Config/value object đã được khởi tạo |

## Nên đặt gì trong `register()`

`register()` dùng để đăng ký service vào container.

Nên đặt ở đây:

```php
public function register(): void
{
    $this->app->singleton(PasswordHasher::class, LaravelPasswordHasher::class);
    $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
}
```

Không nên gọi logic nghiệp vụ, query database hoặc xử lý request trong `register()`. Ở thời điểm này application vẫn đang trong giai đoạn đăng ký service.

## Nên đặt gì trong `boot()`

`boot()` chạy sau khi các service provider đã được register xong.

Nên đặt ở đây các logic khởi động cần toàn bộ application đã sẵn sàng, ví dụ:

```php
public function boot(): void
{
    // Đăng ký event listener, macro, policy, observer...
}
```

Không nên bind service thông thường trong `boot()` nếu có thể làm trong `register()`, vì `register()` là nơi rõ ràng nhất cho container binding.

## Quy tắc thực tế cho project này

- Dùng `bind()` mặc định cho adapter infrastructure đơn giản, không cần giữ instance.
- Dùng `singleton()` cho service stateless và dùng chung an toàn như hasher, formatter, clock, id generator.
- Dùng `scoped()` cho context theo request/job như current user, tenant, correlation id.
- Tránh inject trực tiếp class infrastructure vào Application layer.
- Luôn bind từ interface ở `Application\Contracts` sang implementation ở `Infrastructure`.

Ví dụ đúng:

```php
use App\Core\Application\Contracts\TransactionManager;

public function __construct(
    private readonly TransactionManager $transactionManager,
) {
}
```

Ví dụ nên tránh:

```php
use App\Core\Infrastructure\Persistence\LaravelTransactionManager;

public function __construct(
    private readonly LaravelTransactionManager $transactionManager,
) {
}
```

Lý do: code nghiệp vụ sẽ bị phụ thuộc trực tiếp vào Laravel/infrastructure, làm giảm khả năng test và khó thay implementation về sau.
