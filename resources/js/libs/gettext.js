// resources/js/libs/gettext.js
import { abCreateGettext, initTranslate } from "@appsbd/vue3-appsbd-ui";

let final = {};
if (typeof lang !== "undefined") {
    try {
        final = JSON.parse(JSON.stringify(lang));
    } catch (e) {
        final = lang;
    }
} else if (typeof window !== "undefined" && window.lang) {
    try {
        final = JSON.parse(JSON.stringify(window.lang));
    } catch (e) {
        final = window.lang;
    }
}

const defaultLocale = (typeof app_settings !== "undefined" && app_settings?.locale) ? app_settings.locale : "en";

const config = {
    provideComponent: false,
    provideDirective: false,
    silent: process.env.NODE_ENV === "production",
    availableLanguages: {
        en: "English United States",
        bn: "Bengali Bangladesh",
    },
    defaultLanguage: defaultLocale,
    translations: final,
};

const gettext = abCreateGettext(config);
initTranslate(gettext);

const digits = final?.digits || ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];

function translate_digits(input) {
    if (gettext.current !== "bn") {
        return String(input);
    }
    if (!digits) {
        return String(input);
    }
    return String(input).replace(/\d/g, (d) => digits[d]);
}

String.prototype.translate_digits = function () {
    return translate_digits(this);
};

function convertParamsTranslate_digits(params) {
    if (!params) return {};
    const out = {};
    for (const key in params) {
        const val = params[key];
        out[key] = (typeof val === "number" || typeof val === "string") ? translate_digits(val) : val;
    }
    return out;
}

gettext.translate_digits = translate_digits;
gettext.translate_params_digits = convertParamsTranslate_digits;

export const translateGettext = (msg, params) => {
    if (typeof msg === "undefined" || msg === null) return "";
    if (typeof params === "undefined") {
        params = {};
    }
    if (params && Object.keys(params).length > 0) {
        let cparams = JSON.parse(JSON.stringify(params));
        Object.keys(params).forEach((pr) => {
            let pstr = typeof params[pr] !== "number" ? gettext.$gettext(params[pr]) : params[pr];
            let key = pr;
            if (typeof key === "string" && key.startsWith("n__")) {
                key = key.replace(/^n\_\_/, "");
                cparams[key] = pstr;
            } else {
                cparams[pr] = gettext.translate_digits(pstr);
            }
        });
        return gettext.interpolate(gettext.$gettext(msg), cparams);
    }
    return gettext.$gettext(msg);
};

export const translateGetMsg = (msg, params) => {
    if (typeof msg === "undefined" || msg === null) return "";
    if (typeof params === "undefined") {
        params = {};
    }
    return gettext.translate_digits(gettext.interpolate(gettext.$gettext(msg), params));
};

gettext.translateGettext = translateGettext;
gettext.translateGetMsg = translateGetMsg;

gettext.appDirective = {
    beforeMount(el, binding, vnode) {
        gettext.directive.beforeMount(el, binding, vnode);
        if (gettext.current === "bn") {
            el.innerHTML = translate_digits(el.innerHTML);
        }
    },
    updated(el, binding, vnode) {
        gettext.directive.updated(el, binding, vnode);
        if (gettext.current === "bn") {
            el.innerHTML = translate_digits(el.innerHTML);
        }
    },
};

export { translate_digits };
export default gettext;
