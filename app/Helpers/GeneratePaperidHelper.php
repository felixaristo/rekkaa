<?php 
// use Xendit\Xendit;

use Dotenv\Regex\Success;
use GuzzleHttp\Client;

function paper_generate_invoice($params)
{
    $opts = [
        'invoice_date' => $params['invoice_date'],
        'due_date' => $params['due_date'],
        'description' => $params['description'],
        'number' => $params['number'],
        'customer' => [
            'id' => $params['customer_id'],
            'name' => $params['customer_name'],
            'email' => $params['customer_email'],
            'phone' => $params['customer_phone'],
        ],
        'items' => $params['items'],
        // 'fees' => $params['fees'],
        // "items" => [
        //     [
        //         "name" => "item 01",
        //         "description" => "color is black",
        //         "quantity" => 2,
        //         "price" => 10000,
        //         "discount" => 20,             // Converted to percentage, e.g. 20 will be calculated as 20%
        //         "tax" => 10,                   // Converted to percentage, e.g. 10 will be calculated as 10%
        //         "additional_info" => []
        //     ]
        // ],
        "total" => $params['amount'],
        "signature_text_header" => "1 Mar 2021",
        "signature_text_footer" => "Jane",
        "terms_condition" => "We receive payment no later than 5 days after the bill is received",
        "notes" => "Invoice include service fee",
        'send' => [
            "email" => true,
            "whatsapp" => true,
            "sms" => true
        ],
        // "additional_info" => ($params['additional_info']) ? $params['additional_info'] : null
    ];

    try {
        
        // return json_encode($opts);
    
        $client = new Client([
            'headers' => [ 
                'Content-Type' => 'application/json',
                'client_id' => env('PAPERID_CLIENT_ID'), 
                'client_secret' => env('PAPERID_CLIENT_SECRET'), 
            ]
        ]);
        
        $response = $client->post(env('PAPERID_HOST').'/v1/store-invoice',
            [
                'body' => json_encode($opts)
            ]
        );
        
        $decode_content = json_decode($response->getBody()->getContents());
        // return $decode_content;
        $data = [
            'success' => $decode_content->status_code == 201,
            'message' => $decode_content->data,
            'data' => $decode_content,
            'opts' => $opts
        ];
        return $data;
    } catch (\Throwable $th) {
        $data = [
            'success' => false,
            'message' => $th->getMessage(),
            'opts' => $opts
        ];
        return $data;
        // throw $th;
    }
}