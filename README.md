Email Reply for Yii2
====================

It provides help to configure Yii2 to work with [Tigrov/email-reply](https://github.com/Tigrov/email-reply).

[Tigrov/email-reply](https://github.com/Tigrov/email-reply) library passes reply messages from email to predefined objects. It uses IMAP to connect email servers.

[![Latest Stable Version](https://poser.pugx.org/Tigrov/yii2-email-reply/v/stable)](https://packagist.org/packages/Tigrov/yii2-email-reply)
[![Build Status](https://travis-ci.org/Tigrov/email-reply.svg?branch=master)](https://travis-ci.org/Tigrov/email-reply)

Limitation
----------

The library uses [ddeboer/imap](https://github.com/ddeboer/imap) and it requires:

* PHP >= 7.1
* extensions `iconv`, `IMAP`, `mbstring`

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tigrov/yii2-email-reply
```

or add

```
"tigrov/yii2-email-reply": "~1.0"
```

to the require section of your `composer.json` file.

Usage
-----

Firstly read [Tigrov/email-reply/README](https://github.com/Tigrov/email-reply/blob/master/README.md).

Then you can take steps:

1. Create an ActiveRecord model class with `ModelInterface` interface and implement `emailReply` method (see [examples/Model.php](https://github.com/Tigrov/email-reply/blob/master/examples/Model.php#L53)).
    ```php
    class Model extends yii\db\ActiveRecord implements tigrov\emailReply\ModelInterface
    {
        use tigrov\yii2\emailReply\ActiveRecordThread;
    
        public function emailReply($message)
        {
            /** @var string $fromEmail email address of the sender */
            $fromEmail = $message->getFrom()->getAddress();
            
            /** @var string $fromName name of the sender */
            $fromName = $message->getFrom()->getName();
            
            /** @var string $content content from the replied message */
            $content = $message->getBodyHtml() ?: $message->getBodyText() ?: $message->getDecodedContent();
            
            // Parse the content to get only answer
            $content = EmailReplyParser::parseReply($content);
            
            // To do something with $content
            // e.g. add comment from $fromEmail to the object 
        }
    }
    ```
2. Add `EmailReply` to Yii2 config file.
    ```php
    return [
        ...
        'emailReply' => [
            'class' => 'tigrov\emailReply\EmailReply',
            'classesMap' => [
                // key will be used as prefix for email address
                'm' => \Model::class,
            ],
        ],
        ...
    ];
    ```
3. Send an email message with the special reply email address.
    ```php
    $replyEmail = \Yii::$app->emailReply->getReplyEmail($model);
    
    // Send an email to somebody using the reply email address
    $message = \Yii::$app->mailer->compose($view)
        ->setReplyTo([$replyEmail => \Yii::$app->name])
        ->setFrom([$replyEmail => \Yii::$app->name]);
     
    // Set message subject, body, recipients and etc
    ...
 
    $message->send();
    ```
4. Create the console controller.
    ```php
    class EmailReplyController extends tigrov\yii2\emailReply\EmailReplyController
    {
    }
    ```

5. Read your mailboxes using console command `yii email-reply`. For example as a `cron` job:

    `15 * * * * yii email-reply`
6. Each message will be passed to `Model::emailReply($message)` where you can precess them.

See [examples](https://github.com/tigrov/email-repl/examples) directory for examples.

Suggests
--------
You can use [willdurand/email-reply-parser](https://github.com/willdurand/EmailReplyParser) to parse only reply text from email messages.
```php
$reply = EmailReplyParser::parseReply($content);
```

License
-------

[MIT](LICENSE)
