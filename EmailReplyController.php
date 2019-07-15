<?php
/**
 * @link https://github.com/tigrov/yii2-email-reply
 * @author Sergei Tigrov <rrr-r@ya.ru>
 */

namespace tigrov\yii2\emailReply;

use Ddeboer\Imap\Server;
use tigrov\emailReply\EmailReply;
use tigrov\emailReply\Reader;
use yii\console\Controller;

/**
 * Email reply console controller for Yii2 framework
 * to run use command `yii email-reply` in console or cron
 * it will read mailbox messages and process them by `ModelInterface::emailReply($message)`
 */
class EmailReplyController extends Controller
{
    public $defaultAction = 'read';

    /** @var string \Yii::$app->mailer */
    public $mailer = 'mailer';

    public function actionRead()
    {
        /** @var \Swift_SmtpTransport $transport */
        $transport = \Yii::$app->get($this->mailer)->transport;

        $server = new Server($transport->getHost(), $transport->getPort());
        $connection = $server->authenticate($transport->getUsername(), $transport->getPassword());

        $mailboxModels = Reader::getMailboxModels($connection);
        $messages = Reader::getIterator($mailboxModels);

        /** @var EmailReply $emailReply */
        $emailReply = \Yii::$app->emailReply;
        $emailReply->read($messages);

        $connection->expunge();
    }
}