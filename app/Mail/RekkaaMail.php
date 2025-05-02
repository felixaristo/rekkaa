<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class RekkaaMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $options;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($options)
    {
        //
        $this->options = $options;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = (isset($this->options['data']) && $this->options['data']) ? $this->options['data'] : [];
        if(isset($this->options['attachment']) && $this->options['attachment']) {
            if(isset($this->options['attachment']['isfile']) && $this->options['attachment']['isfile']) {
                return $this->from($this->options['from'])
                ->subject($this->options['subject'])
                ->view($this->options['view'], $data)->attach($this->options['attachment']['file'],
                [
                    'as' => $this->options['attachment']['title'],
                    'mime' => 'application/pdf'
                ]);
            } else {
                return $this->from($this->options['from'])
                ->subject($this->options['subject'])
                ->view($this->options['view'], $data)->attachData($this->options['attachment']['file'],
                $this->options['attachment']['title'],
                [
                    // 'as' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2.pdf',
                    'mime' => 'application/pdf'
                ]);
            }
        } else {
            return $this->from($this->options['from'])
            ->subject($this->options['subject'])
            ->view($this->options['view'], $data);
        }
    }
}
