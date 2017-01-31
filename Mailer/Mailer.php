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
     * @param string $apiKey
     * @param string|false $testMail
     */
    public function __construct(string $apiKey, string $testMail = null)
    {
        $this->client = new Mandrill($apiKey);
        $this->testMail = $testMail;
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
     */
    public function sendTemplateMail(string $email, string $templateName, array $parameters)
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
                'global_merge_vars' => array_values($parameters)
            );
            /* @var $message \struct */
            $result = $this->client->messages->sendTemplate($templateName, array(), $message);
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
