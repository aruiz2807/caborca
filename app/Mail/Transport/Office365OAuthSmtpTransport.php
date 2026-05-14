<?php

namespace App\Mail\Transport;

use App\Services\Office365OAuthTokenService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class Office365OAuthSmtpTransport extends AbstractTransport
{
    public function __construct(
        protected array $config,
        protected Office365OAuthTokenService $tokenService,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct(null, $logger);
    }

    protected function doSend(SentMessage $message): void
    {
        $transport = $this->createInnerTransport();

        try {
            $innerSentMessage = $transport->send($message->getOriginalMessage(), $message->getEnvelope());

            if ($innerSentMessage) {
                $message->setMessageId($innerSentMessage->getMessageId());
                if ($innerSentMessage->getDebug() !== '') {
                    $message->appendDebug($innerSentMessage->getDebug());
                }
            }
        } finally {
            $transport->stop();
        }
    }

    public function __toString(): string
    {
        return sprintf(
            'smtp+office365-oauth2://%s:%s',
            $this->config['host'] ?? 'smtp.office365.com',
            $this->config['port'] ?? 587
        );
    }

    protected function createInnerTransport(): EsmtpTransport
    {
        $host = (string) ($this->config['host'] ?? 'smtp.office365.com');
        $port = (int) ($this->config['port'] ?? 587);
        $encryption = $this->config['encryption'] ?? 'tls';
        $username = (string) ($this->config['oauth_mailbox'] ?? $this->config['username'] ?? '');
        $token = $this->tokenService->getAccessToken($this->config);

        $transport = new EsmtpTransport(
            $host,
            $port,
            $encryption === 'ssl' ? true : null,
            null,
            $this->getLogger(),
            null,
            [new XOAuth2Authenticator()]
        );

        if ($encryption === 'none') {
            $transport->setAutoTls(false);
            $transport->setRequireTls(false);
        } else {
            $transport->setRequireTls($encryption === 'tls');
        }

        $transport->setUsername($username);
        $transport->setPassword($token);

        if (! empty($this->config['local_domain'])) {
            $transport->setLocalDomain((string) $this->config['local_domain']);
        }

        $stream = $transport->getStream();
        if (isset($this->config['timeout'])) {
            $stream->setTimeout((float) $this->config['timeout']);
        }

        return $transport;
    }
}
