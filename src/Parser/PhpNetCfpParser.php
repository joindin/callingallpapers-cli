<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Callingallpapers\Parser;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Writer\WriterInterface;
use GuzzleHttp\Client;

class PhpNetCfpParser implements ParserInterface
{

    public function parse(WriterInterface $writer)
    {
        $uri = 'http://php.net/archive/archive.xml';
        $client = new Client();
        $content = $client->get($uri)->getBody();

        $contents = new \ArrayObject();
        $now = new \DateTimeImmutable();
        $then = $now->sub(new \DateInterval('P1Y'));

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content, LIBXML_NOBLANKS ^ LIBXML_XINCLUDE);
        $dom->documentURI = $uri;

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//xi:include[@href]', $dom->parentNode);

        foreach ($nodes as $item) {
            /** @var \DOMNode $item */
            $href = $item->attributes->getNamedItem('href');
            if (! preg_match('/\/([\d\-]{10})/', $href->textContent, $result)) {
                continue;
            }

            $date = new \DateTime($result[1]);

            if (! $date instanceof \DateTime) {
                continue;
            }

            if ($then > $date) {
                $item->parentNode->removeChild($item);
                continue;
            }
        }

        $dom->xinclude();
        $dom->normalizeDocument();

        $xpath->registerNamespace('default', 'http://php.net/ns/news');
        $xpath->registerNamespace('f', 'http://www.w3.org/2005/Atom');

        $items = $xpath->query('//f:category[@term="cfp"]');

        foreach ($items as $node) {
            try {
                /** @var \DOMNode $node */
                $node    = $node->parentNode;
                $item    = $xpath->query('default:finalTeaserDate', $node)->item(0);
                $cfpDate = new \DateTime($item->textContent);

                if ($now > $cfpDate) {
                    continue;
                }

                $item = $xpath->query('published', $node)->item(0);
                $cfpStart = new \DateTime($item->textContent);
                var_Dump($cfpStart);

                $info = new Cfp();

                $nameNodes            = $xpath->query('f:title', $node);
                $info->conferenceName = $nameNodes->item(0)->textContent;

                $descNode          = $xpath->query('f:content', $node)->item(0);
                $info->description = $dom->saveXML($descNode);

                $info->dateEnd   = $cfpDate;
                $info->dateStart = $cfpStart;
                $info->tags      = ['PHP'];

                $cfpImageNode    = $xpath->query('default:newsImage', $node)->item(0);
                $info->uri       = $cfpImageNode->attributes->getNamedItem('link')->textContent;
                $info->conferenceUri = $cfpImageNode->attributes->getNamedItem('link')->textContent;
                $info->iconUri = 'http://php.net/images/news/' . $cfpImageNode->textContent;

                var_Dump($info->toArray());
          //      $writer->write($info, 'php.net');
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    }
}
