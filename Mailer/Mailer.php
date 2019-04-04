<?php

/*
 * This file is part of the EmharMandrillApp bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\MandrillAppBundle\Mailer;

use Mandrill;
use Psr\Log\LoggerInterface;

class Mailer
{

    /**
     * @var Mandrill
     */
    protected $client;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var null|string
     */
    protected $testMail;

    /**
     * @var array
     */
    protected $templateNames;

    /**
     * @param string $apiKey
     * @param string|false $testMail
     * @param array $templateNames
     */
    public function __construct(string $apiKey, string $testMail = null, array $templateNames)
    {
        $this->client = new Mandrill($apiKey);
        $this->testMail = $testMail;
        $this->templateNames = $templateNames;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send a mandrill app template mail
     *
     * @param string $email
     * @param string $templateName
     * @param array $parameters
     * @param File[]|null $attachmentFiles
     */
    public function sendTemplateMail(string $email, string $templateName, array $parameters, array $attachmentFiles = null)
    {
        $templateName = str_replace('-', '_', $templateName);
        try {
            if ($testEmail = $this->testMail) {
                /* @var $testEmail string */
                $email = $testEmail;
            }
            $to = array(array(
                'email' => $email,
                'type' => 'to'
            ));
            array_walk($parameters, function (&$value, $key) {
                $value = array('name' => $key, 'content' => $value);
            });
            $message = array(
                'to' => $to,
                'global_merge_vars' => array_values($parameters)
            );
            foreach ($attachmentFiles ?? array() as $attachmentFile) {
                $message['attachments'][] = array(
                    'type' => $attachmentFile->getMimeType(),
                    'name' => $attachmentFile->getBasename(),
                    'content' => base64_encode(file_get_contents($attachmentFile->getPathname())),
                );
            }
            /* @var $message \struct */

            $result = $this->client->messages->sendTemplate($this->templateNames[$templateName], array(), $message);
            if ($this->logger) {
                $infoStr = '';
                if (isset($result[0])) {
                    $infoStr .= (isset($result[0]['email']) ? '(email:' . $result[0]['email'] . ')' : '');
                    $infoStr .= (isset($result[0]['status']) ? '(status:' . $result[0]['status'] . ')' : '');
                    $infoStr .= (isset($result[0]['reject_reason']) ? '(reject_reason:' . $result[0]['reject_reason'] . ')'
                        : '');
                    $infoStr .= (isset($result[0]['_id']) ? '(id:' . $result[0]['_id'] : '');
                }
                $this->logger->info('MandrillApp : A message was sent: ' . $infoStr);
            }
        } catch (\Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            if ($this->logger) {
                $this->logger->error('MandrillApp : An error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * Send a mandrill app template mail
     *
     * @param string $email
     * @param string $html
     * @param File[]|null $attachmentFiles
     */
    public function sendHTMLMail(string $email, string $html, array $attachmentFiles = null)
    {
        try {
            if ($testEmail = $this->testMail) {
                /* @var $testEmail string */
                $email = $testEmail;
            }
            $to = array(array(
                'email' => $email,
                'type' => 'to'
            ));
            array_walk($parameters, function (&$value, $key) {
                $value = array('name' => $key, 'content' => $value);
            });
            $message = array(
                'to' => $to,
                'global_merge_vars' => array_values($parameters),
                'html' => $html
            );
            foreach ($attachmentFiles ?? array() as $attachmentFile) {
                $message['attachments'][] = array(
                    'type' => $attachmentFile->getMimeType(),
                    'name' => $attachmentFile->getBasename(),
                    'content' => base64_encode(file_get_contents($attachmentFile->getPathname())),
                );
            }
            /* @var $message \struct */

            $result = $this->client->messages->send($message, array());
            if ($this->logger) {
                $infoStr = '';
                if (isset($result[0])) {
                    $infoStr .= (isset($result[0]['email']) ? '(email:' . $result[0]['email'] . ')' : '');
                    $infoStr .= (isset($result[0]['status']) ? '(status:' . $result[0]['status'] . ')' : '');
                    $infoStr .= (isset($result[0]['reject_reason']) ? '(reject_reason:' . $result[0]['reject_reason'] . ')'
                        : '');
                    $infoStr .= (isset($result[0]['_id']) ? '(id:' . $result[0]['_id'] : '');
                }
                $this->logger->info('MandrillApp : A message was sent: ' . $infoStr);
            }
        } catch (\Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            if ($this->logger) {
                $this->logger->error('MandrillApp : An error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            }
        }
    }
}
