<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Callingallpapers\Entity;

class Cfp
{
    public $conferenceName = '';

    public $conferenceUri = '';

    public $uri = '';

    public $dateEnd = '';

    public $dateStart = '';

    public $description = '';

    public $tags = array();

    public $location = '';

    public $geolocation = '';

    public $eventStartDate = '';

    public $eventEndDate = '';

    public $latitude = 0;

    public $longitude = 0;

    public function __construct()
    {
        $this->dateStart = new \DateTimeImmutable();
        $this->dateEnd   = new \DateTimeImmutable();
        $this->eventStartDate = new \DateTimeImmutable();
        $this->eventEndDate   = new \DateTimeImmutable();
    }

    public function toArray()
    {
        return array(
            'name'           => $this->conferenceName,
            'website_uri'    => $this->conferenceUri,
            'cfp_url'        => $this->uri,
            'cfp_start_date' => $this->dateStart->format('c'),
            'cfp_end_date'   => $this->dateEnd->format('c'),
            'description'    => $this->description,
            'tags'           => $this->tags,
            'start_date'     => $this->eventStartDate->format('c'),
            'end_date'       => $this->eventEndDate->format('c'),
            'location'       => $this->location,
            'latitude'       => (float) $this->latitude,
            'longitude'      => (float) $this->longitude,
        );
    }
}