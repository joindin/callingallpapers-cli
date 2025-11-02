<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     22.09.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Notification;

use Callingallpapers\Entity\Cfp;
use Colorfield\Mastodon\MastodonAPI;
use GuzzleHttp\Psr7\Request;
use function json_encode;

class MastodonNotifier implements NotificationInterface
{
    protected $client;

    public function __construct(MastodonAPI $client)
    {
        $this->client = $client;
    }

    public function notify(Cfp $cfp)
    {
        $name = $this->shortenName($cfp->conferenceName);
        $uri  = $this->shortenUri($cfp->uri);
        $tags = ' #' . implode(' #', $cfp->tags);

        $notificationString = sprintf(
            <<<'NOTIFICATION'
            24 hours until the CfP for "%1$s" closes: %2$s

            #cfp #conference%3$s
            NOTIFICATION,
            $name,
            $uri,
            $tags
        );

        $this->client->post('/statuses', [
            'status' => $notificationString,
            'visibility' => 'public',
        ]);
    }

    protected function shortenName($name)
    {
        if (strlen($name) < 70) {
            return $name;
        }
        return substr($name, 0, 69) . '…';
    }

    protected function shortenUri($uri)
    {
        return $uri;
    }

    protected function formUrlEncode(array $stuff)
    {
        return http_build_query($stuff);
    }
}
