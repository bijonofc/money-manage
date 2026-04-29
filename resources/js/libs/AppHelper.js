import  dayjs  from 'dayjs';
const localeLoaders = {
    en: () => import('dayjs/locale/en'),
    bn: () => import('dayjs/locale/bn'),
    hi: () => import('dayjs/locale/hi'),
    fr: () => import('dayjs/locale/fr'),
};

async function setDayjsLocale(code) {
    const locale = code === 'bn-BD' ? 'bn' : code;

    try {
        if (localeLoaders[locale]) {
            await localeLoaders[locale]();
            dayjs.locale(locale);
        } else {
            dayjs.locale('en');
        }
    } catch (e) {
        dayjs.locale('en');
    }
}
await setDayjsLocale(lang.code);

const AppHelper =
{
    formatDate(str) {

        /*return new Intl.DateTimeFormat('bn-BD', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(new Date(str));*/

        if (!str) {
            return '-';
        }
        return dayjs(str).format('DD MMMM YYYY').translate_digits();
    },
    formatFullDate(str) {
        if(!str){
            return '-';
        }
        return dayjs(str).format('dddd, DD MMMM YYYY').translate_digits(); // Mon, 28 May, 2024
    },
    formatTime(str) {
        if(!str){
            return '-';
        }
        return dayjs(str).format('h:mm A');
    },
    formatDateTime(str) {
        if(!str){
            return '-';
        }
        return dayjs(str).format('DD/MM/YYYY h:mm A').translate_digits();
    },
    formatSmartDateTime(str) {

        const date = dayjs(str);
        const today = dayjs();

        if (date.isSame(today, 'day')) {

            return date.format('h:mm A');
        } else {
            return date.format('DD/MM/YYYY h:mm A').translate_digits();
        }
    },
    toBanglaDigits(number) {
         return number.toString().replace(/\d/g, d => '০১২৩৪৫৬৭৮৯'[d]);
     },
    toLocalizedDigits(number, locale = lang.code) {
        if (number === null || number === undefined) return '-';
        const locales = {
            en: num => num.toString(),
            bn: num => new Intl.NumberFormat('bn-BD').format(num)
        };
        return (locales[locale] || locales.en)(number);
    },
    formatCurrency(amount) {
        if (isNaN(amount)) {
            return amount;
        }
       let currency = app_settings.currencySymbol || '৳';
       let locale = app_settings.locale ||'bn-BD';
        const formatter = new Intl.NumberFormat(locale, { style: 'decimal' });
        return currency + ' ' + formatter.format(amount);
    },
    formatAmount(amount) {
        if (isNaN(amount)) {
            return amount;
        }

       let locale = app_settings.locale ||'bn-BD';
        const formatter = new Intl.NumberFormat(locale, { style: 'decimal' });
        return formatter.format(amount);
    },
    install(Vue, gettext) {
        // translate.gettext=gettext;
        //Vue.config.globalProperties.$appsbdUtls = AppsbdUtls;
    },
}
export default AppHelper;
