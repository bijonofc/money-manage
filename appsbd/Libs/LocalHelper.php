<?php
/**
 * LocalHelper
 *
 * @author: appsbd
 *
 * @package appsbd\Libs
 */


namespace appsbd\Libs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;

class LocalHelper {
    public static $map = null;
    public static function setLocalFormat(){
        Carbon::macro('localDateFormat', function ($format = null) {
            $format = $format ?? env('DATE_FORMAT','d F, Y'); // default app format
            $formatted = $this->translatedFormat( $format );
            return LocalHelper::translateDigits( $formatted );
        });
        Carbon::macro('localDateTimeFormat', function ($format = null) {
            if(is_null($format)) {
                $dateFormat = env( 'DATE_FORMAT', 'd F, Y' ); // default app format
                $timeFormat = env( 'TIME_FORMAT', 'h:i A' ); // default app format
                $format=$dateFormat.' '. $timeFormat;
            }
            $formatted = $this->translatedFormat( $format );
            return LocalHelper::translateDigits( $formatted );
        });
        Blade::directive('localCurrency', function ($amount,$symbol=null) {
            return "<?php echo \appsbd\Libs\LocalHelper::formatCurrency($amount,$symbol); ?>";
        });
        Blade::directive('localDigit', function ($digits) {
            return  "<?php echo \appsbd\Libs\LocalHelper::translateDigits($digits); ?>";
        });
    }
    public static function formatCurrency($amount, $currency = null, $locale = null)
    {
        if (!is_numeric($amount)) {
            return $amount;
        }
        $currency = $currency ?? env('CURRENCY_SYMBOL','৳');
        $locale = $locale ?? app()->getLocale(); // bn_BD / en_US
        $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        return $currency.' '.$formatter->format($amount);

    }
    public static function translateDigits($number, $locale = '')
    {
        if($locale==''){
            $locale = app()->getLocale();
        }
        if(is_null(self::$map)) {
            $fmt = new \NumberFormatter( $locale, \NumberFormatter::DECIMAL );
            $map = [];
            for ( $i = 0; $i <= 9; $i ++ ) {
                $map[ (string) $i ] = $fmt->format( $i );
            }
        }

        return str_replace(
            ['0','1','2','3','4','5','6','7','8','9'],
            $map,
            $number
        );
    }

}
