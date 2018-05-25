<?php

require_once  SHAREDPATH . 'third_party/autoload.php';

/**
 * Error notification to slack channel
 *
 * @author Duy Ton <duytt@nal.vn>
 */
class APP_Error_notifier_driver_slack {

    /**
     * Web hook link
     * @var string
     */
    public $web_hook = null;

    /**
     * Slack channel
     * @var string
     */
    public $channel = null;

    /**
     * From
     * @var null
     */
    public $from = null;

    /**
     * Client
     * @var \Maknz\Slack\Client|null
     */
    public $client = null;

    /**
     * APP_Error_notifier_driver_slack constructor.
     * @param array $params
     */
    public function __construct($params)
    {

        foreach ($params as $key => $value) {
            if (FALSE !== $value) {
                $this->{$key} = $value;
            }
        }

        //
        $this->client = new Maknz\Slack\Client($this->web_hook, [
            'channel' => $this->channel,
            'username' => $this->from
        ]);
    }

    /**
     * 送信
     *
     * @access public
     * @param string $subject
     * @param string $contents
     * @param array $options
     * @return bool
     */
    public function send($subject, $contents, $options = array())
    {
        try {
            $this->client
                ->attach([
                    'text' => $contents,
                    'color' => 'warning'
                ])
                ->send($subject);

        } catch (Exception $e) {
            log_message('debug', $e->getCode() . ': ' . $e->getMessage());
        }
    }
}

