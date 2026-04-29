import {confirmed, email, min,max,max_value,min_value, required,is,url,numeric} from "@vee-validate/rules";
import { defineRule,configure } from 'vee-validate';
import AppsbdUtls from '@/libs/AppsbdUtls.js'
const VeeRules = {
    install(app,translate) {
        const getFieldLabel = (field) =>{
           return field.field.replace('_',' ');
        };
        const allRules={
            required:(value,params,field) => {
                return required(value,params)?true:AppsbdUtls.translateGettext("validate.required",{fld_name:getFieldLabel(field)});
            },
            numeric:(value,params,field) => {
                return numeric(value,params)?true:AppsbdUtls.translateGettext("validate.numeric",{fld_name:getFieldLabel(field)});
            },
            email:(value,params ,field) => {
                return email(value,params)?true:AppsbdUtls.translateGettext("validate.email",{fld_name:getFieldLabel(field)});
            },
            min:(value,params,field) => {
                return min(value,params)?true:AppsbdUtls.translateGettext("validate.min",{fld_name:getFieldLabel(field),params:params[0]});
            },
            min_value:(value,params,field) => {
                return min_value(value,params)?true:AppsbdUtls.translateGettext("validate.min.value",{fld_name:getFieldLabel(field),params:params[0]});
            },
            max:(value,params,field) => {
                return max(value,params)?true:AppsbdUtls.translateGettext("validate.max",{fld_name:getFieldLabel(field),params:params[0]});
            },
            max_value:(value,params,field) => {
                return max_value(value,params)?true:AppsbdUtls.translateGettext("validate.max.value",{fld_name:getFieldLabel(field),params:params[0]});
            },

            confirmed:(value,params,field) => {
                return confirmed(value,params)?true:AppsbdUtls.translateGettext("validate.confirmed",{fld_name:getFieldLabel(field)});
            },
            url:(value,params,field) => {
                return url(value,params)?true:AppsbdUtls.translateGettext("validate.url",{fld_name:getFieldLabel(field)});
            },
            isUnique:async (value,params,field) => {
                if (field == 'email' && !email(value, params, field)) {
                    return true;
                } else if (value.length < 3) {
                    return true;
                }
                return true;
                //let result = await store.dispatch('CheckUnique', {fld_name: field.field, fld_value: value});
               // return result ? true : translateGettext("%{fld_name} is already registered", {fld_name: getFieldLabel(field)});
            },
            isValid:async (value,params,field) => {
                if(params[0]=='custom') {
                    let minLength=3;
                    if(params[1] != undefined) {
                        if (params[2] != undefined) {
                            minLength = params[2];
                        }
                        if (value.length >= minLength) {

                            let result = await store.dispatch('IsValidCF', {fld_name: params[1], fld_value: value});
                            return result.status ? true : translate.interpolate(result.msg, {fld_name: getFieldLabel(field)});
                        } else {
                            return AppsbdUtls.translateGettext("validate.not_valid", {fld_name: getFieldLabel(field)});
                        }
                    }
                   return true;
                }else{
                    return true;
                }
            },
            uniqueValidation:async (value,param,field) => {
                console.log(param);
                if (value) {
                    if (!param.loader) {
                        param.loader = function (status) {
                        }
                    }
                    param.loader(true);
                    let response = await param.callBack(value);
                    param.loader(false);
                    if (!response) {
                        return translateGettext("%{fld_name} is already registered", {fld_name: getFieldLabel(field)});
                    } else {
                        return true;
                    }
                }
                return true;

            },
            is:(value,params,field) => {
                console.log(value);
                console.log(params);
                console.log(field);
                return is(value,params)?true:AppsbdUtls.translateGettext("validate.is",{fld_name:getFieldLabel(field)});
            },
        }
        configure({
            bails: true,
            validateOnInput: true,
            validateOnMount:false,
        });
        Object.keys(allRules).forEach(rule => {
            defineRule(rule, allRules[rule]);
        });
    },
}
export default VeeRules;
