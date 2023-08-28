<?php
use yii\payment\enum\Status;
use yii\payment\exceptions\InvalidProviderConfig;
use yii\payment\Response;

class RazorPayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;
    private string $successPaymentId = 'pay_MVNt5SxhE7u8gz';
    private string $failedPaymentId = 'pay_MUi95hlolc8upp';

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

    public function testUnknownPaymentProvider()
    {
        $this->assertThrows(\yii\payment\exceptions\ProviderNotFound::class, function () {
            \YII::$app->payment->process('unknown', $this->failedPaymentId);
        });
    }

    public function testInvalidConfigurationOfProvider()
    {
        $this->assertThrows(InvalidProviderConfig::class, function () {
            \Yii::$app->set('payment', [
                'class' => \yii\payment\Gateway::class,
                'logging' => [
                    'connection' => 'db',
                    'tableName' => 'ginni_payment_logs'
                ],
                'providers' => [
                    'unknown' => [
                        'class' => \yii\payment\provider\RazorPay::class
                    ]
                ]
            ]);
            \YII::$app->payment->process('unknown', $this->failedPaymentId);
        });
    }

    public function testRazorPaySelectedPaymentGatewayProvider()
    {
        $response = \YII::$app->payment->process('razorpay', $this->successPaymentId);
        $this->assertTrue($response->getProvider() === 'yii\payment\provider\RazorPay');
    }

    public function testInvalidExternalCredentials()
    {
        $this->assertThrows(InvalidProviderConfig::class, function () {
             \YII::$app->payment->process('razorpay', $this->successPaymentId,[
                'apiSecret'=> null,
                'apiKey'=> null
            ]);
        });

    }
}
