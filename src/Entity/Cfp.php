<?php
/**
 * Copyright (c) 2015-2016 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2015-2016 Andreas Heigl/callingallpapers.com
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     01.12.2015
 * @link      http://github.com/heiglandreas/callingallpapers-cli
 */
namespace Callingallpapers\Entity;

class Cfp
{
    /** @var string  */
    public $conferenceName = '';

    /** @var string  */
    public $conferenceUri = '';

    /** @var string  */
    public $uri = '';

    /** @var \DateTimeInterface */
    public $dateEnd;

    /** @var \DateTimeInterface  */
    public $dateStart;

    /** @var string  */
    public $description = '';

    /** @var array  */
    public $tags = array();

    /** @var string  */
    public $location = '';

    /** @var string */
    public $geolocation = '';

    /** @var \DateTimeInterface  */
    public $eventStartDate;

    /** @var \DateTimeInterface  */
    public $eventEndDate;

    /** @var float */
    public $latitude = 0.0;

    /** @var float  */
    public $longitude = 0.0;

    /** @var string  */
    public $timezone = 'UTC';

    /** @var string  */
    public $iconUri = '';

    public function __construct()
    {
        $this->dateStart      = new \DateTimeImmutable();
        $this->dateEnd        = new \DateTimeImmutable();
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
            'tags'           => array_unique($this->tags),
            'start_date'     => $this->eventStartDate->format('c'),
            'end_date'       => $this->eventEndDate->format('c'),
            'location'       => $this->location,
            'latitude'       => (float) $this->latitude,
            'longitude'      => (float) $this->longitude,
            'timezone'       => $this->timezone,
        );
    }

    public function addTag($tag)
    {
        if (! in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }
}
