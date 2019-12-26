<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Service\Payment\TinkoffMerchantAPI;

class Payment {
    const STATE_SUCCESS = 2;


    private $username;
    private $password;
    private $url;

    public function __construct($live = false, $username, $password)
    {

        $this->username = $username;
        $this->password = $password;

        $this->url = 'https://securepay.tinkoff.ru/v2/';
    }

    public function payByOrder(Order $order, $returnUrl)
    {
        return $this->pay([
            'orderNumber' => $order->getCode(),
            'amount' => urlencode($order->getPriceForPayment()),
            'returnUrl' => $returnUrl
        ]);
    }

    public function isSuccessOrder($orderId)
    {
        $orderStatus = $this->orderStatus($orderId);

        return $orderStatus['OrderStatus'] == self::STATE_SUCCESS;
    }

    public function isSuccessOrderTinkoff($orderId)
    {
        $state = $this->getState($orderId);
        $res = json_decode($state, true);

        return $res['Status'] == 'CONFIRMED';
    }

    public function pay($data)
    {
        return $this->gateway('register.do', $data);
    }

    public function orderStatus($id, $type = 'orderId')
    {
        return $this->gateway('getOrderStatus.do', [
            $type => $id
        ]);
    }

    /**
     * For Tinkoff
     */
    public function makePayment(Order $order, $email, $price, $productName)
    {
        $api = $this->createTinkoff();
        
        $quantity = 1;
        $amount = $price * $quantity;
        $receipt = [
            'Email' => $email,
            'Taxation' => 'osn',
            'Items' => [
                [
                    'Name' => $productName,
                    "Price" => $price,
                    "Quantity" => $quantity,
                    "Amount" => $price * $quantity,
                    "Tax" => 'none'
                ],
            ]
        ];

        $params = array(
            'OrderId' => $order->getCode(),
            'Amount' => $amount,
            'DATA' => array(
                'Email' => $email,
                'Connection_type' => 'example'
            ),
        );
        $params['Receipt'] = $receipt;
        
        $api->init($params);
        $res = [];
        
        if ($api->error) {
            $res = [
                'errorCode' => strlen($api->error),
                'errorMessage' => $api->error,
            ];
        } else {
            $res = [
                'orderId' => $api->paymentId,
                'formUrl' => $api->paymentUrl
            ];
        }

        return $res;
    }

    protected function createTinkoff()
    {
        return new TinkoffMerchantAPI(
            $this->username,  //Ваш Terminal_Key
            $this->password,   //Ваш Secret_Key
            $this->url
        );
    }

    public function getState($paymentId)
    {
        $res = $this->createTinkoff()->getState(['PaymentId' => $paymentId]);
        return $res;
    }

    public function gateway($method, $data) {
        $data['userName'] = $this->username;
        $data['password'] = $this->password;

        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . $method, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $response = curl_exec($curl); // Выполняем запрос

        $response = json_decode($response, true); // Декодируем из JSON в массив
        curl_close($curl); // Закрываем соединение
        return $response; // Возвращаем ответ
    }

}