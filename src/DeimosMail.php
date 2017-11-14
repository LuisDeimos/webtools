<?php

namespace Deimos;

use Symfony\Component\Yaml\Yaml;

/**
 * Class DeimosMail
 */
final class DeimosMail
{
    private $subject;
    private $to;
    private $from;
    private $body;
    private $yaml = __DIR__.'/../../../../dm_config.yml';
    private $userEmail;
    private $userPass;
    private $domain;
    private $port;
    private $encryption;

    /**
     * DeimosMail constructor.
     * @param string $config
     * @throws \Exception
     */
    public function __construct($config = 'deimos_mail')
    {
        if (!file_exists($this->yaml)) {
            throw new \Exception(sprintf('You must first create and configure the %s file configuration.', $this->yaml));
        }

        $data = Yaml::parse(file_get_contents($this->yaml));

        if (!array_key_exists($config, $data)) {
            throw new \Exception(sprintf('You must first configure the %s file configuration.', $this->yaml));
        }

        $this->userEmail = $data[$config]['email'];
        $this->userPass = $data[$config]['password'];
        $this->domain = $data[$config]['domain'];
        $this->port = $data[$config]['port'];
        $this->encryption = array_key_exists('encryption', $data[$config]) ? $data[$config]['encryption'] : null;

    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed
     */
    public function send()
    {
        $transport = (new \Swift_SmtpTransport($this->domain, $this->port, $this->encryption))
            ->setUsername($this->userEmail)
            ->setPassword($this->userPass);

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message($this->subject))
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody($this->body);

        return $mailer->send($message);
    }

}
