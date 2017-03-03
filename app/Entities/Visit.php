<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;

class Visit implements Jsonable
{
    /** @var string */
    protected $route;

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
    protected $referrer;

    const UNKNOWN_BROWSER = 'Unknown browser';

    const UNKNOWN_PLATFORM = 'Unknown platform';

    public function __construct(Request $request)
    {
        $this->route = $request->getUri();
        $this->browser = $this->extractBrowser($request);
        $this->os = $this->extractOs($request);
        $this->ip = $request->ip();
        $this->cookieHash = static::hashCookies($request->cookies->all());
        $this->date = (new Carbon());
        $this->geo = 'geo'; // TODO!
        $this->referrer = $request->server->has('HTTP_REFERER') ? $request->server->get('HTTP_REFERER') : null;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Visit
     */
    public function setRoute($route)
    {
        $this->route = $route;

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
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return Visit
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;

        return $this;
    }

    /**
     * @param $cookies
     * @return string
     */
    public static function hashCookies($cookies)
    {
        return md5(json_encode($cookies));
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function extractOs(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        $os = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach ($os as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                return $value;
            }
        }

        return Visit::UNKNOWN_PLATFORM;
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function extractBrowser(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {

            if (preg_match($regex, $userAgent)) {
                return $value;
            }

        }

        return Visit::UNKNOWN_BROWSER;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $jsonObj = new \stdClass();

        $jsonObj->route = $this->route;
        $jsonObj->browser = $this->browser;
        $jsonObj->os = $this->os;
        $jsonObj->ip = $this->ip;
        $jsonObj->cookieHash = $this->cookieHash;
        $jsonObj->date = $this->date->toDateTimeString();
        $jsonObj->geo = $this->geo;
        $jsonObj->referrer = $this->referrer;

        return json_encode($jsonObj);
    }
}
