<?php
use yii\payment\enum\Status;
use yii\payment\Response;

class RazorPayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;
    private string $successPaymentId = 'pay_MIj1KqWnQh5n2C';
    private string $failedPaymentId = 'pay_MIjGN36C6sUrXi';

    protected function _before(): void
    {
        parent::_before();
    }

    public function testDBMigration()
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
