<?php
use yii\payment\enum\Status;
use yii\payment\Response;

class RazorPayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;
    private string $successPaymentId;
    private string $failedPaymentId;

    protected function _before(): void
    {
        Yii::$app->get('db', )->dsn = 'mysql:host=127.0.0.1;port=' . $_ENV['MYSQL_PORT'] . ';dbname=paymentdb';
        $this->successPaymentId = $_ENV['SUCCESS_PAYMENT_ID'];
        $this->failedPaymentId = $_ENV['FAILED_PAYMENT_ID'];
        parent::_before();
    }

    public function testMysqlConnection()
    {
        \Yii::$app->runAction('migrate/up', [
            'interactive' => 0
        ]);
    }

    public function testFetchPaymentBySuccesPaymentId()
    {
        $response = \YII::$app->payment->process('razorpay', $this->successPaymentId);
        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->getPaymentId() === $this->successPaymentId);
        $this->assertTrue($response->getStatus() === Status::SUCCESS->value);
    }

    public function testFetchPaymentByFailedPaymentId()
    {
        $this->assertThrows(\yii\payment\exceptions\BadRequest::class, function () {
            \YII::$app->payment->process('razorpay', $this->failedPaymentId);
        });
    }
}
