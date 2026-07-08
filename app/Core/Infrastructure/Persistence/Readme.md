# Persistence Layer

Thư mục `Persistence` thuộc tầng `Infrastructure`, chịu trách nhiệm hiện thực các cơ chế lưu trữ dữ liệu và transaction bằng công cụ cụ thể của framework hoặc database.

Trong kiến trúc DDD/Clean Architecture, tầng `Domain` và `Application` không nên phụ thuộc trực tiếp vào Laravel, Eloquent, Query Builder, `DB` facade hoặc chi tiết kết nối cơ sở dữ liệu. Những chi tiết đó được đặt ở tầng `Infrastructure/Persistence` để có thể thay đổi công nghệ lưu trữ mà không làm rò rỉ framework vào logic nghiệp vụ.

## Vai trò chính

- Hiện thực các contract liên quan đến lưu trữ dữ liệu được định nghĩa ở tầng `Application`.
- Đóng gói chi tiết kỹ thuật của Laravel/database, ví dụ `DB::transaction`.
- Cung cấp adapter để application service/use case có thể thao tác với dữ liệu thông qua interface.
- Giữ cho domain model và use case tập trung vào nghiệp vụ, không phụ thuộc vào framework.

## File hiện có

### `LaravelTransactionManager.php`

Class này hiện thực contract:

```php
App\Core\Application\Contracts\TransactionManager
```

Nó sử dụng `Illuminate\Support\Facades\DB` để chạy callback trong database transaction:

```php
DB::transaction($callback, attempts: 3);
```

Ý nghĩa:

- Nếu callback chạy thành công, Laravel sẽ tự động `commit`.
- Nếu callback ném exception, Laravel sẽ tự động `roll back`.
- `attempts: 3` cho phép Laravel thử lại transaction khi gặp lỗi deadlock phù hợp.
- Giá trị trả về của callback được trả ra ngoài qua `run()`.

## Cách sử dụng trong Application layer

Application layer chỉ nên phụ thuộc vào interface `TransactionManager`, không phụ thuộc trực tiếp vào `LaravelTransactionManager`.

Ví dụ:

```php
use App\Core\Application\Contracts\TransactionManager;

final class CreateUserHandler
{
    public function __construct(
        private readonly TransactionManager $transactionManager,
    ) {
    }

    public function handle(CreateUserCommand $command): mixed
    {
        return $this->transactionManager->run(function () use ($command) {
            // Gọi repository, lưu aggregate, phát domain event nếu cần...
            // Nếu có exception, toàn bộ thay đổi dữ liệu sẽ được rollback.
        });
    }
}
```

Với cách này, use case chỉ biết rằng nó đang chạy trong một transaction, còn transaction đó được thực thi bằng Laravel, database nào, hoặc cơ chế nào là chi tiết của Infrastructure.

## Nguyên tắc đặt code trong thư mục này

Nên đặt ở đây:

- Repository implementation dùng Eloquent, Query Builder hoặc raw SQL.
- Unit of Work hoặc Transaction Manager implementation.
- Mapper chuyển đổi giữa persistence model và domain model.
- Data access object hoặc gateway phục vụ lưu trữ.
- Các adapter cụ thể cho database/cache/storage nếu chúng liên quan trực tiếp đến persistence.

Không nên đặt ở đây:

- Entity, value object, domain service hoặc rule nghiệp vụ.
- Use case, command handler, query handler thuộc Application layer.
- Controller, request, response hoặc middleware HTTP.
- Code xử lý authentication, hashing, queue, mail nếu không liên quan trực tiếp đến lưu trữ dữ liệu.

## Quy ước phụ thuộc

Hướng phụ thuộc mong muốn:

```text
Domain <- Application <- Infrastructure
```

Điều này có nghĩa:

- `Domain` không biết `Application` và `Infrastructure`.
- `Application` có thể định nghĩa contract như `TransactionManager`.
- `Infrastructure` hiện thực contract đó bằng công nghệ cụ thể như Laravel.
- Code nghiệp vụ gọi interface, không gọi trực tiếp `DB`, Eloquent hoặc class infrastructure.

## Khi mở rộng Persistence layer

Khi cần thêm repository hoặc persistence adapter mới, nên đi theo các bước:

1. Định nghĩa contract ở tầng `Application` nếu use case cần phụ thuộc vào nó.
2. Hiện thực contract ở `Infrastructure/Persistence`.
3. Bind interface với implementation trong service provider.
4. Giữ mapping giữa database model và domain model ở tầng Infrastructure.
5. Viết test cho behavior quan trọng, đặc biệt với transaction, rollback và mapping dữ liệu.

Ví dụ cấu trúc có thể mở rộng sau này:

```text
Persistence/
├── LaravelTransactionManager.php
├── Eloquent/
│   └── EloquentUserRepository.php
├── Mappers/
│   └── UserMapper.php
└── Readme.md
```

## Ghi chú thiết kế

`Persistence` không phải nơi chứa nghiệp vụ. Nó là lớp chuyển đổi giữa nhu cầu của ứng dụng và hạ tầng lưu trữ thật.

Nếu một đoạn code trả lời câu hỏi "nghiệp vụ cần làm gì?", nó thường thuộc `Domain` hoặc `Application`.

Nếu một đoạn code trả lời câu hỏi "lưu, đọc, commit, rollback bằng công nghệ nào?", nó thường thuộc `Infrastructure/Persistence`.
