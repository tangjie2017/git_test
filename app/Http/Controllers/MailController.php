<?php

namespace App\Http\Controllers;

use App\Auth\Controllers\BaseAuthController;
use Illuminate\Support\Facades\Mail;


class MailController extends BaseAuthController
{

    public function send($content,$to,$subject)
    {
        Mail::send(
            'emails.notice',
            ['content' => $content],
            function ($message) use($to, $subject) {
                $message->to($to)->subject($subject);
            }
        );

    }



}
