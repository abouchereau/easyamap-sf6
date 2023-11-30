<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

//use App\Entity\PaymentFreq;
use Symfony\Component\Intl\Locales;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * See https://symfony.com/doc/current/templating/twig_extension.html.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Julien ITARD <julienitard@gmail.com>
 */
final class AppExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    private readonly array $localeCodes;

    /**
     * @var list<array{code: string, name: string}>|null
     */
    private ?array $locales = null;

    protected $mois_court = array('','Jan.','Fév.','Mar.','Avr.','Mai','Juin','Juil.','Août','Sep.','Oct.','Nov.','Déc.');
    protected $mois_long = array('','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
    protected $jour_long = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');

    // The $locales argument is injected thanks to the service container.
    // See https://symfony.com/doc/current/service_container.html#binding-arguments-by-name-or-type
    public function __construct(string $locales)
    {
        $localeCodes = explode('|', $locales);
        sort($localeCodes);
        $this->localeCodes = $localeCodes;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', $this->getLocales(...))
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_fr', array($this, 'date_fr')),
            new TwigFilter('price_fr', array($this, 'price_fr')),
            new TwigFilter('price_excel', array($this, 'price_excel')),
            new TwigFilter('month_year_fr', array($this, 'month_year_fr')),
            new TwigFilter('month_long_fr', array($this, 'month_long_fr')),
            new TwigFilter('type_fr', array($this, 'type_fr')),
            new TwigFilter('slugify', array($this, 'slugify')),
            new TwigFilter('full_date_fr', array($this, 'full_date_fr')),
            new TwigFilter('full_date_fr2', array($this, 'full_date_fr2')),
            new TwigFilter('date_small', array($this, 'date_small')),
            new TwigFilter('description_payment', array($this, 'description_payment')),
            new TwigFilter('creneau', array($this, 'creneau')),
            new TwigFilter('two_digits', array($this, 'two_digits')),
            new TwigFilter('json_decode', 'json_decode'),
            new TwigFilter('addslashes', 'addslashes'),
            new TwigFilter('mois', array($this, 'mois')),
            new TwigFilter('encodeMail', array($this, 'encodeMail')),
        ];
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     *
     * @return array<int, array<string, string>>
     */
    public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];
        foreach ($this->localeCodes as $localeCode) {
            $this->locales[] = ['code' => $localeCode, 'name' => Locales::getName($localeCode, $localeCode)];
        }

        return $this->locales;
    }



    public function mois($input) {
        return  $this->mois_court[(int)$input];
    }

    public function date_fr($date_en)
    {
        $tmp = explode('-',$date_en);
        return $tmp[2].' '.$this->mois_court[(int)$tmp[1]];
    }

    public function month_long_fr($month) {
        return $this->mois_long[(int)$month];
    }

    public function month_year_fr($date_en, $short=false) {
        $tmp = explode('-',$date_en);
        if ($short)
            return $this->mois_court[(int)$tmp[1]].' '.$tmp[0];
        else
            return $this->mois_long[(int)$tmp[1]].' '.$tmp[0];
    }

    public function date_small($date) {
        $tmp = explode('-',$date);
        return $tmp[2].'/'.$tmp[1].'/'.substr($tmp[0],2,2);
    }

    public function price_fr($num) {
        return number_format($num, 2, ',',' ').' €';
    }

    public function price_excel($num) {
        return number_format($num, 2, ',','');//number_format($num, 2, ',','');
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }

    public function type_fr($type) {
        switch ($type) {
            case 'report': return 'Compte-rendu';break;
            case 'shipping': return 'Livraison';break;
            case 'ventilation': return 'Ventilation';break;
            case 'payment': return 'Paiements';break;
        }
    }

    public function full_date_fr($dateTime) {
        $str = "";
        $str .= mb_strtolower($this->jour_long[$dateTime->format('w')]);
        $str .= " ".$dateTime->format('j')." ";
        $str .= mb_strtolower($this->mois_long[$dateTime->format('n')]);
        $str .= " ".$dateTime->format('Y');
        return $str;
    }

    public function full_date_fr2($str) {
        if (strlen($str)==10) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $str);
            return $this->full_date_fr($dateTime);
        }
        return "--";
    }

  /*  public function creneau($str,$freq) {
        if (PaymentFreq::getNbMonth($freq)===false) {
            return '----';
        }
        elseif (PaymentFreq::getNbMonth($freq) == 1) {//sur un mois
            return $this->month_year_fr($str,true);
        } else {//sur plusieurs mois
            $dateTime = \DateTime::createFromFormat('Y-m-d', $str);
            $dateTime1 = clone $dateTime;
            $interval = new \DateInterval('P'.(PaymentFreq::getNbMonth($freq)-1).'M');
            $dateTime->add($interval);
            $dateTime2 = clone $dateTime;
            if($dateTime1->format('Y') != $dateTime2->format('Y')) {//à cheval sur 2 années
                return $this->mois_court[$dateTime1->format('n')].' '.$dateTime1->format('Y').'-'.$this->mois_court[$dateTime2->format('n')].' '.$dateTime2->format('Y');
            }
            else {
                return $this->mois_court[$dateTime1->format('n')].'-'.$this->mois_court[$dateTime2->format('n')].' '.$dateTime2->format('Y');
            }
        }
    }*/

    public function two_digits($num) {
        if ($num<10) {
            return '0'.$num;
        }
        return ''.$num;
    }

    public function encodeMail($text)
    {
        $encoded_text = '';
        for ($i = 0; $i < strlen($text); $i++)
        {
            $char = $text[$i];
            $r = rand(0, 100);
            # roughly 10% raw, 45% hex, 45% dec
            # '@' *must* be encoded. I insist.
            if ($r > 90 && $char != '@')
                $encoded_text .= $char;
            else if ($r < 45)
                $encoded_text .= '&#x'.dechex(ord($char)).';';
            else
                $encoded_text .= '&#'.ord($char).';';
        }
        return $encoded_text;
    }
}
