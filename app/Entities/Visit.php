<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;

class Visit implements Jsonable
{
    /** @var string */
    protected $uri;

    /** @var string */
    protected $browser;

    /** @var string */
    protected $os;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $cookieHash;

    /** @var Carbon */
    protected $date;

    /** @var string */
    protected $geo;

    /** @var string */
    protected $referer;

    const NO_REFERER = 'No referer';

    /**
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request)
    {
        // this one is used to get browser and platform information
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->header('User-Agent'));

        // geo
        $geo = geoip($request->getClientIp());
        $location = $geo->getAttribute('country') . ', ' . $geo->getAttribute('city');

        $instance = new static();
        $instance->uri = $request->getRequestUri();
        $instance->browser = $agent->browser();
        $instance->os = $agent->platform();
        $instance->ip = $request->getClientIp();
        $instance->cookieHash = static::hashCookies($request->cookies->all());
        $instance->date = (new Carbon());
        $instance->geo = $location;
        $instance->referer = $request->server->has('HTTP_REFERER') ?
            static::extractHost($request->server->get('HTTP_REFERER')) :
            static::NO_REFERER;

        return $instance;
    }

    /**
     * @param string $json
     * @return static
     */
    public static function fromJson($json)
    {
        $jsonObject = json_decode($json);

        if (!is_object($jsonObject)) {
            throw new \InvalidArgumentException('Invalid JSON string!');
        }

        // iterate through properties and set ours
        $instance = new static();
        foreach (get_object_vars($jsonObject) as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return Visit
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param string $browser
     * @return Visit
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string $os
     * @return Visit
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return Visit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getCookieHash()
    {
        return $this->cookieHash;
    }

    /**
     * @param string $cookieHash
     * @return Visit
     */
    public function setCookieHash($cookieHash)
    {
        $this->cookieHash = $cookieHash;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Carbon $date
     * @return Visit
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @param string $geo
     * @return Visit
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param string $referer
     * @return Visit
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Hashes cookies
     *
     * @param array $cookies
     * @return string
     */
    public static function hashCookies($cookies)
    {
        return md5(json_encode($cookies));
    }

    /**
     * Extracts host from url
     *
     * @param string $url
     * @return string mixed
     */
    public static function extractHost($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $jsonObj = new \stdClass();

        $jsonObj->uri = $this->uri;
        $jsonObj->browser = $this->browser;
        $jsonObj->os = $this->os;
        $jsonObj->ip = $this->ip;
        $jsonObj->cookieHash = $this->cookieHash;
        $jsonObj->date = $this->date->toDateTimeString();
        $jsonObj->geo = $this->geo;
        $jsonObj->referer = $this->referer;

        return json_encode($jsonObj);
    }
}
