<?php 
use Xendit\Xendit;

function xendit_generate_invoice($params)
{
    $opts = [
        'external_id' => $params['external_id'],
        'amount' => $params['amount'],
        'description' => $params['description'],
        'invoice_duration' => $params['invoice_duration'],
        'customer' => [
            'given_names' => $params['name'],
            'surname' => $params['name'],
            'email' => $params['email'],
            'mobile_number' => $params['phone'],
            'addresses' => [
                [
                    'city' => $params['city'],
                    'country' => $params['country'],
                    'postal_code' => $params['postal_code'],
                    'state' => $params['city'],
                    'street_line1' => $params['address'],
                ]
            ]
        ],
        'customer_notification_preference' => [
            'invoice_created' => [
                'whatsapp',
                'sms',
                'email',
                'viber'
            ],
            'invoice_reminder' => [
                'whatsapp',
                'sms',
                'email',
                'viber'
            ],
            'invoice_paid' => [
                'whatsapp',
                'sms',
                'email',
                'viber'
            ],
            'invoice_expired' => [
                'whatsapp',
                'sms',
                'email',
                'viber'
            ]
        ],
        'success_redirect_url' => env('XENDIT_SUCCESS_REDIRECT'),
        'failure_redirect_url' => env('XENDIT_FAILED_REDIRECT'),
        'currency' => 'IDR',
        'items' => $params['items'],
        'fees' => $params['fees']
    ];

    // dd(env('XENDIT_SECRET_API_KEY'));
    Xendit::setApiKey(env('XENDIT_SECRET_API_KEY'));
    $createInvoice = \Xendit\Invoice::create($opts);
    return $createInvoice;
}