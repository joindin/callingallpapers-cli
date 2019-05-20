<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\Description;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class EventDescriptionTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    public function testThatLocationIsReturnedCorrectly($file, $result)
    {
        $parser = new Description();

        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;

        $dom->load($file, LIBXML_NOBLANKS ^ LIBXML_NOERROR ^ LIBXML_NOENT);

        $this->assertEquals($result, $parser->parse($dom, new DOMXPath($dom)));
    }

    public function uriIsReturnedCorrectlyProvider() : array
    {
        return [[
            __DIR__ . '/../__assets/Azure Day Rome 2019: Call for Speakers @ Sessionize.com.html',
            '<p>Benvenuto in Azure Day Rome 2019, l\'evento 
gratuito dedicato a tutti gli utenti che desiderano conoscere o 
approfondire la conoscenza di Azure per lo sviluppo e l\'amministrazione 
di soluzioni applicative nel Cloud!</p><p> </p><p>La conferenza Azure Day Rome 2019 (#adrome2019) avverrà il 24 Maggio, presso la sede Microsoft di Romaa Via Avignone 10. </p><p>La
 conferenza, organizzata dalla community DotNetCode, tratterà come 
argomento principale Azure e sarà agnostica alla tecnologia senza 
necessariamente essere legata al mondo .NET.<br/><p>La conferenza sarà suddivisa in due track: Dev e DevOps.<br/><p>- La track Dev tratterà tutti quegli argomenti legati ai servizi che Azure mette a disposizione per gli sviluppatori.
</p><p>- La track DevOps trattera tutti quegli argomenti necessari per 
l\'utilizzo di tecniche di DevOps che si possono implementare all\'interno
 di Azure.</p>
                    </p>
                </p>',
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            '<p>En Globant Medellín vamos a realizar la 
segunda "React Week" de nuestra historia y estamos buscando Speakers. Si
 te interesa compartir tu conocimiento sobre React, ayudar a otros 
desarrolladores, propiciar discusiones o simplemente mostrar Algo de tu 
trabajo, anímate a participar tenemos el Call for proposals abierto.</p><p><br/><p>¿Que es la React Week?</p><p><br/><p>Es
 una semana en la cual de martes a sábado tendremos charlas diaria de 
5:30PM a 7:00PM y el sábado workshops de 9:00AM a 1:00PM sobre una 
temática del framework en particular.</p><p><br/><p>¿Donde se realizará?</p><p><br/><p>En el chill principal de Globant - Medellín. Centro Empresaríal Vizcaya Oficina 123B. </p><p>Cl. 10 ##32-115, Medellín, Antioquia</p><p><br/><p>¿Cuando se realizará?</p><p><br/><p>Desde el Martes 05 de Marzo al Sábado 09 de Marzo.</p><p><br/><p>¿A quién esta dirigido?</p><p><br/><p>A
 todas las personas interesadas en aprender, practicar o reforzar sus 
conocimiento sobre React/Redux. Tendremos participantes de algunas 
comunidades de la ciudad y excelentes sorpresas.</p></p></p></p></p></p><div class="col-md-6 animated fadeInRight" id="right-column"><div class="ibox float-e-margins"><div class="ibox-title"><div class="pull-right"><span class="badge badge-danger">finished 4 days ago</span></div><h5>Call for Speakers</h5></div><div class="ibox-content"><div class="row"><div class="col-sm-6 m-b-sm"><div><span class="font-bold text-navy">CfS opens at 12:00 AM</span></div><h2 class="no-margins">21 Jan 2019</h2><small/></div><div class="col-sm-6 m-b-sm"><div><span class="font-bold text-navy">CfS closes at 11:59 PM</span></div><h2 class="no-margins">29 Jan 2019</h2></div></div><div class="row"><div class="col-sm-12 m-b-md"><small class="text-muted">
                            This event is in SA Pacific Standard Time time zone.
                        </small><div class="displayNone" style="display: block;"><small class="text-muted">
                                Closing time in your time zone is <span class="js-closedate" data-date="2019-01-30T04:59:00.0000000Z">30 Jan 2019 at 5:59 AM</span>.
                            </small></div></div></div><div class="row"><div class="col-md-12"><hr class="m-t-none"><p>Algunos temassugeridos:</p><p><br/><p>React Foundations.</p><p>State manager: Redux, MobX, flux.</p><p>Style Components.</p><p>React Native</p><p>React Integrations</p><p>Jest Unit Testing in React</p><p>End to End Testing in React</p></p></hr></div></div></div></div></div></p></p></p></form><div class="cookiebanner" style="position: fixed; left: 0px; right: 0px; height: auto; min-height: 21px; z-index: 2055; background: rgb(244, 244, 244) none repeat scroll 0% 0%; color: rgb(51, 51, 51); line-height: 21px; padding: 5px 16px; font-family: arial, sans-serif; font-size: 11px; text-align: left; bottom: 0px; opacity: 1;"><div class="cookiebanner-close" style="float: left; padding-left: 5px; padding-right: 5px; color: rgb(23, 157, 130); cursor: pointer;">✖</div><span>We use cookies to improve your browsing experience. <a href="https://sessionize.com/privacy-policy/" target="_blank" style="text-decoration: none; color: rgb(170, 170, 170); font-weight: normal;">Details</a></span></div></div>'
        ],];
    }
}
