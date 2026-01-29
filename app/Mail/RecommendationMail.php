<?php

namespace App\Mail;

use App\Models\Person;
use App\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecommendationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $recommendation;
    public $person;

    public function __construct(Recommendation $recommendation, Person $person)
    {

        $this->recommendation = $recommendation;
        $this->person = $person;
    }

    public function build()
    {

        return $this->subject('Դուք ստացել եք  մարզչի խորհուրդներ')
                    ->view('emails.recommendation');
    }

    /**
     * Get the message content definition.
     */
    
}
