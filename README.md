## 🛠 Installation

You can install the package via composer:

```bash
composer require inzamam/selise-block-authorization
```
## ⚙️ Configuration

To publish the config file:

```bash
php artisan vendor:publish --tag=selise-block-authorization-config
```
This will publish the config file to:

```arduino
config/selise-block-authorization.php
```

### `.env` setting

Setup the environment variables in your .env:

```dotenv
BLOCK_API_URL=https://your-api-url.com
BLOCK_API_ORIGIN=https://your-origin.com
BLOCK_USER=your-user
BLOCK_PASSWORD=your-password
BLOCK_CLIENT_ID=your-client-id
BLOCK_API_VERSION=v1
```

## 🧩 Migrations

To publish the migration for block_access_tokens and block_api_logs:

```bash
php artisan vendor:publish --tag=selise-block-authorization-migrations
php artisan migrate
```

Or publish everything (config + migrations) at once:

```bash
php artisan vendor:publish --tag=selise-block-authorization
php artisan migrate
```

## 🚀 Usage

Use the facade or bound service to retrieve and store access tokens:

```php
use SeliseBlockAuthService;

$token = SeliseBlockAuthService::getAccessToken();
echo $token;
```

## 🧑‍💻 Author
Inzamamul Karim
inzamamul.karim@selisegroup.com

### 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)