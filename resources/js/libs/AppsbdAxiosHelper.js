import axios from "axios";
import ObjectToFormData from "@/libs/ObjectToFormData";
import { useLoginStore } from '@/modules/AdminPanel/User/loginStore.js'


axios.interceptors.response.use(function (response,) {
    return response;
}, function (error) {
    if (error.response && (401 === error.response.status || 419 ===error.response.status )) {
        const store = useLoginStore();
        store.redirectToLogin();
    }
    else {
        return Promise.reject(error);
    }
});
function get_header(isMultiPart,is_public){
    if(typeof is_public =='undefined'){
        is_public=false;
        axios.defaults.withCredentials=true;
        axios.defaults.withXSRFToken=true;
    }
    if (is_public)
    {
        axios.defaults.withCredentials=false;
    }
    let config= {
        headers: {
            'Accept':'application/vnd.api+json',
            'Content-Type': isMultiPart?'': 'application/x-www-form-urlencoded'
        }
    }
    return config;
}
const AxiosHelper={
    ObjectToQueryString:function(obj, prefix){
        var str = [], k, v;
        for(var p in obj) {
            if (!obj.hasOwnProperty(p)) {continue;} // skip things from the prototype
            if (~p.indexOf('[')) {
                k = prefix ? prefix + "[" + p.substring(0, p.indexOf('[')) + "]" + p.substring(p.indexOf('[')) : p;
                // only put whatever is before the bracket into new brackets; append the rest
            } else {
                k = prefix ? prefix + "[" + p + "]" : p;
            }
            v = obj[p];
            if (v !== null && v !== undefined && v !== '')
            {
                str.push(typeof v == "object" ?
                    AxiosHelper.ObjectToQueryString(v, k) :
                    encodeURIComponent(k) + "=" + encodeURIComponent(v));
            }
        }
        return str.join("&");
    },
    public_post:function(url,params,isMultiPart){
        let queryparams={}
        if(isMultiPart){
            queryparams= ObjectToFormData(params);
        }else{
            queryparams = AxiosHelper.ObjectToQueryString(params);

        }
        return axios.post(url,queryparams,get_header(isMultiPart,true));
    },
    post:function(url,params,isMultiPart){
        const store = useLoginStore();
        if (!store.isLoggedIn) {
            // Return a rejected Promise with an appropriate error
            return Promise.reject(new Error('User is not logged in'));
        }
        let queryparams={}
        if(isMultiPart){
            queryparams= ObjectToFormData(params);
        }else{
            queryparams = AxiosHelper.ObjectToQueryString(params);

        }
        return axios.post(url,queryparams,get_header(isMultiPart));
    },
    put:function(url,params,isMultiPart){
        let queryparams={}
        if(isMultiPart){
            queryparams= ObjectToFormData(params);
        }else{
            queryparams = AxiosHelper.ObjectToQueryString(params);

        }
        return axios.put(url,queryparams,get_header(isMultiPart));
    },
    patch:function(url,params,isMultiPart){
        let queryparams={}
        if(isMultiPart){
            queryparams= ObjectToFormData(params);
            queryparams.append('_method', 'PATCH');
            console.log(queryparams);
            return axios.post(url,queryparams,get_header(isMultiPart));
        }else{
            queryparams = AxiosHelper.ObjectToQueryString(params);
            return axios.patch(url,queryparams,get_header(isMultiPart));
        }
    },
    delete:function(url,params,isMultiPart){
        let config=get_header(isMultiPart);
        if(params) {
            config.data = params;
        }

        return axios.delete(url,config);
    },
    get:function(url){
        return axios.get(url,get_header(false));
    },
    crc32:function(r){
        if(typeof r =='object'){
            r=JSON.stringify(r);
        }
        for(var a,o=[],c=0;c<256;c++){a=c;for(var f=0;f<8;f++)a=1&a?3988292384^a>>>1:a>>>1;o[c]=a}for(var n=-1,t=0;t<r.length;t++)n=n>>>8^o[255&(n^r.charCodeAt(t))];
        return(-1^n)>>>0;
    },
    errorHandler:function(error){
        try {
            if (error?.response?.status === 403 && error?.response?.data) {
                return error.response.data;
            }
        }catch (e) {}
        return {status:false, msg: {error:[error.message],info:[]},data:null};
    }

}


export default AxiosHelper;
