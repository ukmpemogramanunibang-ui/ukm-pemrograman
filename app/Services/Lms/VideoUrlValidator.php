<?php

namespace App\Services\Lms;

class VideoUrlValidator
{
    protected array $providers = [
        'youtube'   => '/(youtube\.com|youtu\.be)/i',
        'vimeo'     => '/vimeo\.com/i',
        'instagram' => '/instagram\.com/i',
        'tiktok'    => '/tiktok\.com/i',
    ];

    /**
     * @return array{ok: bool, provider: ?string, message: ?string}
     */
    public function validate(?string $url): array
    {
        if (! $url || trim($url) === '') {
            return ['ok' => true, 'provider' => null, 'message' => null];
        }

        foreach ($this->providers as $name => $pattern) {
            if (preg_match($pattern, $url)) {
                return ['ok' => true, 'provider' => $name, 'message' => null];
            }
        }

        return [
            'ok'       => false,
            'provider' => null,
            'message'  => 'Link video harus dari YouTube, Vimeo, Instagram, atau TikTok.',
        ];
    }
}