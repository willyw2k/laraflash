<?php

namespace Coderello\Laraflash;

use Throwable;
use Coderello\Laraflash\Exceptions\InvalidDelayException;
use Coderello\Laraflash\Exceptions\InvalidArgumentException;
use Coderello\Laraflash\Exceptions\InvalidHopsAmountException;
use Coderello\Laraflash\Contracts\FlashMessage as FlashMessageContract;

class FlashMessage implements FlashMessageContract
{
    /**
     * @var array
     */
    const MUTABLE_PROPERTIES = ['title', 'content', 'type', 'hops', 'delay', 'important'];

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var int|null
     */
    protected $hops;

    /**
     * @var int|null
     */
    protected $delay;

    /**
     * @var bool|null
     */
    protected $important;

    /**
     * FlashMessage constructor.
     */
    public function __construct()
    {
        $this->hops(1);

        $this->delay(1);

        $this->important(false);
    }

    /**
     * Set the title for the current FlashMessage instance.
     *
     * @param string $title
     *
     * @return FlashMessage
     */
    public function title(string $title): FlashMessageContract
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the content for the current FlashMessage instance.
     *
     * @param string $content
     *
     * @return FlashMessage
     */
    public function content(string $content): FlashMessageContract
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the type for the current FlashMessage instance.
     *
     * @param string $type
     *
     * @return FlashMessage
     */
    public function type(string $type): FlashMessageContract
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the hops amount for the current FlashMessage instance.
     *
     * @param int $hops
     *
     * @throws InvalidHopsAmountException
     *
     * @return FlashMessage
     */
    public function hops(int $hops): FlashMessageContract
    {
        if ($hops < 1) {
            throw new InvalidHopsAmountException;
        }

        $this->hops = $hops;

        return $this;
    }

    /**
     * Set the delay for the current FlashMessage instance.
     *
     * @param int $delay
     *
     * @throws InvalidDelayException
     *
     * @return FlashMessage
     */
    public function delay(int $delay): FlashMessageContract
    {
        if ($delay < 0) {
            throw new InvalidDelayException;
        }

        $this->delay = $delay;

        return $this;
    }

    /**
     * Set the important flag for the current FlashMessage instance.
     *
     * @param bool $important
     *
     * @return FlashMessage
     */
    public function important(bool $important = true): FlashMessageContract
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Show the message during the current request.
     *
     * @return FlashMessage
     */
    public function now(): FlashMessageContract
    {
        $this->delay(0);

        return $this;
    }

    /**
     * Keep the message for one more request.
     *
     * @return FlashMessage
     */
    public function keep(): FlashMessageContract
    {
        $this->hops++;

        return $this;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     *
     * @throws Throwable
     */
    public function render(): string
    {
        return view(config('laraflash.skin'), $this->toArray())->render();
    }

    /**
     * Data which should be serialized to JSON.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this, $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_reduce(self::MUTABLE_PROPERTIES, function (array $accumulator, string $property) {
            $accumulator[$property] = $this->{$property};

            return $accumulator;
        }, []);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->isMutableProperty($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (! $this->isMutableProperty($offset)) {
            throw new InvalidArgumentException;
        }

        return $this->{$offset};
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (! $this->isMutableProperty($offset)) {
            throw new InvalidArgumentException;
        }

        $this->{$offset}($value);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        //
    }

    /**
     * Whether a property is mutable.
     *
     * @param string $property
     *
     * @return bool
     */
    protected function isMutableProperty(string $property): bool
    {
        return in_array($property, self::MUTABLE_PROPERTIES);
    }
}
