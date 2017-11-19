# Aliyun/QCloud Queue Extension for Yii 2

[![Latest Stable Version](https://poser.pugx.org/xutl/yii2-queue/v/stable.png)](https://packagist.org/packages/xutl/yii2-queue)
[![Total Downloads](https://poser.pugx.org/xutl/yii2-queue/downloads.png)](https://packagist.org/packages/xutl/yii2-queue)
[![Dependency Status](https://www.versioneye.com/php/xutl:yii2-queue/dev-master/badge.png)](https://www.versioneye.com/php/xutl:yii2-queue/dev-master)
[![License](https://poser.pugx.org/xutl/yii2-queue/license.svg)](https://packagist.org/packages/xutl/yii2-queue)

这个扩展是给官方原版的 `yiisoft/yii2-queue` 增加了 阿里云/腾讯云 消息队列驱动，只有队列，没有主题。按下面说明配置上以后，就可以按照官方原版的说明开始使用即可。

Installation
------------

Next steps will guide you through the process of installing using [composer](http://getcomposer.org/download/). Installation is a quick and easy three-step process.

### Step 1: Install component via composer

Either run

```
composer require --prefer-dist xutl/yii2-queue
```

or add

```json
"xutl/yii2-queue": "~2.0.0"
```

to the `require` section of your composer.json.

### Step 2: Configuring your application

Add following lines to your main configuration file:

```php
    'components' => [
        'queue' => [
            'class' => 'xutl\queue\aliyun\Queue',
            'endPoint' => 'http://aabbcc.mns.cn-hangzhou.aliyuncs.com/',
            'accessId' => '1234567',
            'accessKey' => '654141234',
            'queue' => 'task',
        ],
        'queue2' => [
            'class' => 'xutl\queue\qcloud\Queue',
            'endPoint' => 'http://aabbcc.mns.cn-hangzhou.aliyuncs.com/',
            'region' => 'bj',
            'accessId' => '1234567',
            'accessKey' => '654141234',
            'queue' => 'task',
        ],
    ],
```

Read [https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/README.md](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/README.md) to continue

## License

This is released under the MIT License. See the bundled [LICENSE.md](LICENSE.md)
for details.
