Simple Page Provider for Silex
==============================

シンプルなページ情報の REST API を提供します。

インストール
----------

`composer.json` に次のようなコードを追加します。

```javascript
{
    "repositories": [
    {
        "type": "package",
        "package": {
            "name": "masakielastic/silex-simplepage",
            "version": "0.1.0",
            "type": "package",
            "source": {
                "url": "https://github.com/masakielastic/silex-simplepage.git",
                "type": "git",
                "reference": "master"
            },
            "autoload": {
                "psr-4": { "Masakielastic\\Silex\\": "src/" }
            }
        }
    }
    ],
    "require": {
        "silex/silex": "~1.3",
        "doctrine/dbal":"~2.2",
        "masakielastic/silex-simplepage": "*"
    }
}
```

コントローラープロバイダーを次のように Silex のアプリケーションにマウントします。

```php
use Silex\Application;
use Silex\Provider;
use Masakielastic\Silex\SimplePageControllerProvider;

$app = new Application();
$app->register(new Provider\DoctrineServiceProvider());
$app['db.options'] = [
    'driver'   => 'pdo_sqlite',
    'path'     => __DIR__.'/app.db'
];
$app->mount('/api', new SimplePageControllerProvider());

$app->run();
```

テーブルの作成は次のコードを参照してください。SQlite を対象としています。

```php
$app->get('/api/reset', function(Application $app) {

    $sqls = [
        'DROP TABLE IF EXISTS page',
        'CREATE TABLE IF NOT EXISTS page('.
        '  id INTEGER PRIMARY KEY AUTOINCREMENT,'.
        '  name TEXT NOT NULL,'.
        '  title TEXT,'.
        '  body TEXT,'.
        '  unique(name)'.
        ');',
        "INSERT INTO page(name, title, body) VALUES('index', 'ホームの見出し', 'ホームの本文');",
        "INSERT INTO page(name, title, body) VALUES('about', '自己紹介の見出し', '自己紹介の本文');",
        "INSERT INTO page(name, title, body) VALUES('contact', '問い合わせの見出し', '問い合わせの本文');"
    ];

    foreach ($sqls as $sql) {
        $app['db']->query($sql); 
    }

    return $app->json(['msg' => 'ok']);
});
```
    

API
---

 * GET /api/pages
 * GET /api/pages?name={name}
 * POST /api/pages
 * PUT /api/pages/{id}
 * PATCH /api/pages/{id}
 * DELETE /api/pages/{id}