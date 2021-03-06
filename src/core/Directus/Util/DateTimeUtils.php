<?php

namespace Directus\Util;

use DateTimeZone;

class DateTimeUtils extends \DateTime
{
    /**
     * Seconds in a minute
     *
     * @var int
     */
    const MINUTE_IN_SECONDS = 60;

    /**
     * Seconds in a day
     *
     * @var int
     */
    const DAY_IN_SECONDS = 86400;

    /**
     * Seconds in a week
     *
     * @var int
     */
    const WEEK_IN_SECONDS = self::DAY_IN_SECONDS * 7;

    /**
     * @var string
     */
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * DateTime modifier format days into the future
     *
     * @var string
     */
    const ADD_DAYS_FORMAT = '+%s days';

    /**
     * DateTime modifier format days ago
     *
     * @var string
     */
    const SUB_DAYS_FORMAT = '-%s days';

    public function __construct($time = null, $timezone = null)
    {
        $timezone = $this->createTimeZone($timezone);

        parent::__construct($time, $timezone);

        if ($time === null) {
            $this->setTimestamp(time());
        }
    }

    /**
     * Gets the current datetime
     *
     * @param null $timezone
     *
     * @return DateTimeUtils
     */
    public static function now($timezone = null)
    {
        return new static(null, $timezone);
    }

    public static function nowInUTC()
    {
        return static::now('UTC');
    }

    /**
     * Creates a new DateTimeUtils instance from a \DateTime instance
     *
     * @param \DateTime $dateTime
     *
     * @return DateTimeUtils
     */
    public static function createFromDateTime(\DateTime $dateTime)
    {
        return new static($dateTime->format(static::DEFAULT_DATETIME_FORMAT), $dateTime->getTimezone());
    }

    /**
     * @param null $time
     * @param null $timezone
     *
     * @return DateTimeUtils
     */
    public static function createFromDefaultFormat($time = null, $timezone = null)
    {
        return static::createDateFromFormat(static::DEFAULT_DATETIME_FORMAT, $time, $timezone);
    }

    /**
     * @param string $format
     * @param string $time
     * @param null $timezone
     *
     * @return DateTimeUtils
     */
    public static function createDateFromFormat($format, $time, $timezone = null)
    {
        $instance = parent::createFromFormat($format, $time, static::createTimeZone($timezone));

        return static::createFromDateTime($instance);
    }

    /**
     * @param int $days
     *
     * @return DateTimeUtils
     */
    public static function inDays($days)
    {
        return static::now()->addDays($days);
    }

    /**
     * @param int $days
     *
     * @return DateTimeUtils
     */
    public static function wasDays($days)
    {
        return static::now()->subDays($days);
    }

    /**
     * Creates a timezone object
     *
     * @param string|DateTimeZone $timezone
     *
     * @return DateTimeZone
     */
    public static function createTimeZone($timezone)
    {
        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }

        if ($timezone === null) {
            return new DateTimeZone(date_default_timezone_get());
        }

        try {
            $timezone = new DateTimeZone($timezone);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf('Unknown or bad timezone (%s)', $timezone)
            );
        }

        return $timezone;
    }

    /**
     * Switch date time to a different timezone
     *
     * @param $timezone
     *
     * @return DateTimeUtils
     */
    public function switchToTimeZone($timezone)
    {
        $this->setTimezone($this->createTimeZone($timezone));

        return $this;
    }

    /**
     * Add days to the current date time
     *
     * @param int $days
     *
     * @return DateTimeUtils
     */
    public function addDays($days)
    {
        return $this->modify(sprintf(static::ADD_DAYS_FORMAT, (int)$days));
    }

    /**
     * Subtract days from teh current date time
     *
     * @param int $days
     *
     * @return DateTimeUtils
     */
    public function subDays($days)
    {
        return $this->modify(sprintf(static::SUB_DAYS_FORMAT, (int)$days));
    }

    /**
     * Gets the datetime in GMT
     *
     * @return DateTimeUtils
     */
    public function toGMT()
    {
        return $this->toTimeZone('GMT');
    }

    /**
     * Gets the datetime string in GMT
     *
     * @return string
     */
    public function toGMTString()
    {
        return $this->toGMT()->toString();
    }

    /**
     * Returns the datetime in ISO 8601 format
     *
     * @return string
     */
    public function toISO8601Format()
    {
        return $this->format('c');
    }

    /**
     * Returns a string format of the datetime
     *
     * @param null $format
     *
     * @return string
     */
    public function toString($format = null)
    {
        if ($format === null) {
            $format = static::DEFAULT_DATETIME_FORMAT;
        }

        return $this->format($format);
    }

    /**
     * Creates a copy of the current datetime to a new timezone
     *
     * @param string|DateTimeZone $timezone
     *
     * @return DateTimeUtils
     */
    public function toTimeZone($timezone)
    {
        return $this->getCopy()->setTimezone($this->createTimeZone($timezone));
    }

    /**
     * Gets a copy of the instance
     *
     * @return DateTimeUtils
     */
    public function getCopy()
    {
        return clone $this;
    }
}
