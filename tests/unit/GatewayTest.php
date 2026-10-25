<?php

use data\RazorPayStub;
use yii\payment\enum\Status;
use yii\payment\exceptions\BadRequest;
use yii\payment\exceptions\InvalidProviderConfig;
use yii\payment\Gateway;
use yii\payment\logging\models\Sql;
use yii\payment\Response;

/**
 * Gateway tests that run without network access or Razorpay credentials.
 */
class GatewayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    private const SUCCESS_ID = 'pay_stubSuccess01';
    private const FAILED_ID = 'pay_stubFailed001';
    private const NO_CONTACT_ID = 'pay_stubNoContact';

    protected function _before(): void
    {
        parent::_before();

        RazorPayStub::$credentials = [];
        RazorPayStub::$payments = [
            self::SUCCESS_ID => [
                'id' => self::SUCCESS_ID,
                'entity' => 'payment',
                'amount' => 50000,
                'currency' => 'INR',
                'status' => 'authorized',
                'contact' => '+919999999999',
                'email' => 'buyer@example.com',
            ],
            self::FAILED_ID => [
                'id' => self::FAILED_ID,
                'entity' => 'payment',
                'amount' => 1000,
                'currency' => 'INR',
                'status' => 'failed',
                'contact' => '+919999999998',
                'error_description' => 'Payment was declined by the bank.',
            ],
            self::NO_CONTACT_ID => [
                'id' => self::NO_CONTACT_ID,
                'entity' => 'payment',
                'amount' => 1000,
                'currency' => 'INR',
                'status' => 'authorized',
                'contact' => null,
            ],
        ];

        $this->setGateway();
        \Yii::$app->runAction('migrate/up', ['interactive' => 0]);
        Sql::deleteAll();
    }

    private function setGateway(array|false $logging = ['connection' => 'db', 'tableName' => 'ginni_payment_logs']): void
    {
        \Yii::$app->set('payment', [
            'class' => Gateway::class,
            'logging' => $logging,
            'providers' => [
                'razorpay' => [
                    'class' => RazorPayStub::class,
                    'apiKey' => 'config-key',
                    'apiSecret' => 'config-secret',
                ],
            ],
        ]);
    }

    public function testSuccessfulPaymentIsReturnedAndLogged()
    {
        $response = \Yii::$app->payment->process('razorpay', self::SUCCESS_ID);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(self::SUCCESS_ID, $response->getPaymentId());
        $this->assertSame(Status::SUCCESS->value, $response->getStatus());
        $this->assertSame(50000.0, $response->getAmount());
        $this->assertSame('INR', $response->getCurrency());
        $this->assertSame('+919999999999', $response->getContactPhone());
        $this->assertSame(RazorPayStub::class, $response->getProvider());
        $this->assertNull($response->getError());
        $this->assertSame([['config-key', 'config-secret']], RazorPayStub::$credentials);

        $record = Sql::findOne(['payment_id' => self::SUCCESS_ID]);
        $this->assertNotNull($record);
        $this->assertSame(Status::SUCCESS->value, $record->status);
        $this->assertSame(RazorPayStub::$payments[self::SUCCESS_ID], json_decode($record->raw, true));
    }

    public function testFailedPaymentThrowsAndIsLogged()
    {
        try {
            \Yii::$app->payment->process('razorpay', self::FAILED_ID);
            $this->fail('BadRequest was not thrown.');
        } catch (BadRequest $e) {
            $this->assertSame('Payment was declined by the bank.', $e->getMessage());
            $this->assertNotNull($e->getResponse());
            $this->assertSame(self::FAILED_ID, $e->getResponse()->getPaymentId());
            $this->assertSame(Status::FAILED->value, $e->getResponse()->getStatus());
        }

        $record = Sql::findOne(['payment_id' => self::FAILED_ID]);
        $this->assertNotNull($record);
        $this->assertSame(Status::FAILED->value, $record->status);
    }

    public function testFailedPaymentThrowsWhenLoggingIsDisabled()
    {
        $this->setGateway(false);

        $this->assertThrowsWithMessage(BadRequest::class, 'Payment was declined by the bank.', function () {
            \Yii::$app->payment->process('razorpay', self::FAILED_ID);
        });
        $this->assertSame(0, (int) (new \yii\db\Query())->from('ginni_payment_logs')->count());
    }

    public function testPaymentWithoutContactPhone()
    {
        $response = \Yii::$app->payment->process('razorpay', self::NO_CONTACT_ID);

        $this->assertNull($response->getContactPhone());
        $this->assertNotNull(Sql::findOne(['payment_id' => self::NO_CONTACT_ID]));
    }

    public function testRuntimeCredentialsOverrideConfiguredOnes()
    {
        \Yii::$app->payment->process('razorpay', self::SUCCESS_ID, [
            'apiKey' => 'runtime-key',
            'apiSecret' => 'runtime-secret',
        ]);

        $this->assertSame([['runtime-key', 'runtime-secret']], RazorPayStub::$credentials);
    }

    public function testProviderWithoutApiSecretIsRejected()
    {
        \Yii::$app->set('payment', [
            'class' => Gateway::class,
            'providers' => [
                'razorpay' => [
                    'class' => RazorPayStub::class,
                    'apiKey' => 'config-key',
                ],
            ],
        ]);

        $this->assertThrows(InvalidProviderConfig::class, function () {
            \Yii::$app->payment->process('razorpay', self::SUCCESS_ID);
        });
    }

    public function testUpdateRecordByPaymentId()
    {
        \Yii::$app->payment->process('razorpay', self::SUCCESS_ID);

        $logger = \Yii::$app->payment->getLogger();
        $this->assertTrue($logger->updateRecordByPaymentId(self::SUCCESS_ID, ['status' => 'REFUNDED']));
        $this->assertSame('REFUNDED', Sql::findOne(['payment_id' => self::SUCCESS_ID])->status);
        $this->assertFalse($logger->updateRecordByPaymentId('pay_doesNotExist', ['status' => 'REFUNDED']));
    }

    public function testGatewayWithoutProvidersIsRejected()
    {
        $this->assertThrows(\yii\base\InvalidConfigException::class, function () {
            \Yii::createObject(['class' => Gateway::class]);
        });
    }
}
