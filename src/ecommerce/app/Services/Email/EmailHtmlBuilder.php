<?php

declare(strict_types=1);

namespace App\Services\Email;

class EmailHtmlBuilder
{
    /**
     * Wrap html.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $fromEmail
     *
     * @return string
     * @throws \Throwable
     */
    public function buildHtmlWrapper(
        string $to,
        string $subject,
        string $content,
        string $fromEmail
    ): string {
        return view('emails.html_wrapper', [
            'fromEmail' => $fromEmail,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
        ])->render();
    }
}
