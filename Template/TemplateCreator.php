<?php

/*
 * This file is part of the EmharMandrillApp bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\MandrillAppBundle\Template;

use Psr\Log\LoggerInterface;

class TemplateCreator
{
    /**
     * @var \Mandrill
     */
    protected $client;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var array
     */
    protected $existingTemplates;

    /**
     * @var array
     */
    protected $templateNames;

    /**
     * @param string $apiKey
     * @param array $templateNames
     */
    public function __construct(string $apiKey, array $templateNames)
    {
        $this->client = new \Mandrill($apiKey);
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
     * @param string $name
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $text
     */
    public function createTemplate($from_email, $from_name, $name, $subject, $html)
    {
        if (!$this->existingTemplates) {
            $this->existingTemplates = $this->client->templates->getList();
            $this->existingTemplates = array_map(function ($a) {
                return $a['name'];
            }, $this->existingTemplates);
        }
        if (in_array($this->templateNames[$name], $this->existingTemplates)) {
            $result = $this->client->templates->update($this->templateNames[$name], $from_email, $from_name, $subject, $html);
            return $result['slug'];
        }
        $result = $this->client->templates->add($this->templateNames[$name], $from_email, $from_name, $subject, $html);
        return $result['slug'];
    }
}
