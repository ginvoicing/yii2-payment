<?php

class GatewayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected function _before(): void
    {
        Yii::$app->get('db', )->dsn = 'mysql:host=127.0.0.1;port=' . $_ENV['MYSQL_PORT'] . ';dbname=currencydb';
        Yii::$app->set('swiftcurrency', [
            'class' => \yii\payment\Gateway::class,
            'baseCurrency' => 'EUR',
            'caching' => [
                'connection' => 'db',
                'tableName' => 'ginni_currency_exchange_rates'
            ],
            'providers' => [
                [
                    'class' => \yii\payment\provider\CurrencyScoop::class,
                    'apiKey' => $_ENV['CURRENCY_SCOOP_API']
                ],
                [
                    'class' => \yii\payment\provider\Fixer::class,
                    'apiKey' => $_ENV['FIXER_API']
                ],
                [
                    'class' => \yii\payment\provider\OpenExchangeRates::class,
                    'apiKey' => $_ENV['OPEN_EXCHANGE_API']
                ],
                [
                    'class' => \yii\payment\provider\ExchangeRatesAPI::class,
                    'apiKey' => $_ENV['EXCHANGE_RATE_API']
                ],
            ]
        ]);
        parent::_before();
    }

    public function testMysqlConnection()
    {
        \Yii::$app->runAction('migrate/up', [
            'interactive' => 0
        ]);
    }


    public function testUnknownPropertyException()
    {
        $this->assertThrows(\yii\base\UnknownPropertyException::class, function () {
            \Yii::$app->get('swiftcurrency')->unknown;
        });
    }

    public function testExchangeRates()
    {
        $this->assertTrue(\Yii::$app->swiftcurrency->pullRatesFromProvider()->getStatus() == \yii\payment\enum\Status::SUCCESS());
    }
}