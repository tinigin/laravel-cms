<?php

namespace LaravelCms\Alert;

use Illuminate\Session\Store;
use LaravelCms\Support\Color;

/**
 * Class Alert.
 */
class Alert
{
    /**
     * @var string
     */
    public const SESSION_MESSAGE_TITLE = 'flash_notification.title';

    /**
     * @var string
     */
    public const SESSION_MESSAGE = 'flash_notification.message';

    /**
     * @var string
     */
    public const SESSION_LEVEL = 'flash_notification.level';

    /**
     * @var Store
     */
    protected $session;

    /**
     * Create a new flash notifier instance.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Flash an information message.
     *
     * @param string $title
     * @param string $message
     *
     * @return Alert
     */
    public function info(string $title, string $message = ''): self
    {
        $this->message($title, $message);

        return $this;
    }

    /**
     * Flash a general message.
     *
     * @param string $title
     * @param string $message
     * @param Color|null $level
     *
     * @return Alert
     */
    public function message(string $title, string $message = '', Color $level = null): self
    {
        $level = $level ?? Color::INFO();

        $this->session->flash(static::SESSION_MESSAGE_TITLE, $title);
        $this->session->flash(static::SESSION_MESSAGE, $message);
        $this->session->flash(static::SESSION_LEVEL, (string) $level);

        return $this;
    }

    /**
     * Flash a success message.
     *
     * @param string $title
     * @param string $message
     *
     * @return Alert
     */
    public function success(string $title, string $message = ''): self
    {
        $this->message($title, $message, Color::SUCCESS());

        return $this;
    }

    /**
     * Flash an error message.
     *
     * @param string $title
     * @param string $message
     *
     * @return Alert
     */
    public function error(string $title, string $message = ''): self
    {
        $this->message($title, $message, Color::ERROR());

        return $this;
    }

    /**
     * Flash a warning message.
     *
     * @param string $title
     * @param string $message
     *
     * @return Alert
     */
    public function warning(string $title, string $message = ''): self
    {
        $this->message($title, $message, Color::WARNING());

        return $this;
    }

    /**
     * Checks if a message has been set before.
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->session->has(static::SESSION_MESSAGE);
    }
}
