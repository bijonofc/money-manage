// resources/js/plugins/gettext.js
import { computed, defineComponent, getCurrentInstance, h, onMounted, ref } from "vue";
import { createGettext,useGettext } from "vue3-gettext";

const config = {
    provideComponent:false,
    provideDirective:false,
    silent: process.env.NODE_ENV === "production",
    availableLanguages: {
        bn_BD: "Bengali Bangladesh"
    },
    defaultLanguage: app_settings.locale,
    translations:lang
};
const gettext =createGettext(config);

const digits=lang?.digits;
function translate_digits(input) {
    if(!digits) {
        return String(input);
    }
    return String(input).replace(/\d/g, d => digits[d]);
}
String.prototype.translate_digits=function(){
    return translate_digits(this);
}
function convertParamsTranslate_digits(params) {
    if (!params) return {};
    const out = {};
    for (const key in params) {
        const val = params[key];
        out[key] = (typeof val === 'number' || typeof val === 'string') ? translate_digits(val) : val;
    }
    return out;
}

// সংখ্যাসহ টেক্সট অনুবাদের জন্য এক্সটেন্ডেড মেথড
gettext.translate_digits =translate_digits;
gettext.translate_params_digits=convertParamsTranslate_digits;

gettext.translateGettext = (msg,params) => {
    if (typeof params == 'undefined') {
        params = {};
    }
    if (params) {
        let cparams = JSON.parse(JSON.stringify(params));
        Object.keys(params).forEach(pr => {
            let pstr = (typeof (params[pr]) !== "number") ? gettext.$gettext(params[pr]) : params[pr];
            let key = pr;
            if (typeof key === 'string' && key.startsWith('n__')) {
                key = key.replace(/^n\_\_/, '');
                cparams[key] = pstr;
            } else {
                cparams[pr] = gettext.translate_digits(pstr);
            }

        });
        return gettext.interpolate(gettext.$gettext(msg), cparams);
    }
    return gettext.interpolate(gettext.$gettext(msg), params);
}
gettext.translateGetMsg= (msg,params) =>{
    if(typeof params =='undefined'){
        params= {};
    }
    return   gettext.translate_digits(gettext.interpolate(gettext.$gettext(msg),params));
}











gettext.appDirective = {
    beforeMount(el, binding, vnode) {

       // el.innerHTML=gettext.translate_digits(el.innerHTML);
        gettext.directive.beforeMount(el, binding, vnode);
        el.innerHTML=translate_digits(el.innerHTML);
    },
    updated(el, binding, vnode) {
       // el.innerHTML=gettext.translate_digits(el.innerHTML);
        gettext.directive.updated(el, binding, vnode);
        el.innerHTML=translate_digits(el.innerHTML);
    }
};




export default gettext
