import { computed, ref,reactive } from 'vue'
import Swal from "sweetalert2";
import { useToast,createToastInterface,POSITION} from "vue-toastification";

const settings={
    base_slug:'',
    ajax_url:null,
    api_url:'/'
}
const app_data=reactive({
    darkMode:false
});
const darkMode=reactive(false);

const setBrowserDarkStatus=()=>{
    let isDark=sessionStorage.getItem('apbd_dark');
    if(isDark) {
        setDarkMode(isDark == 'Y');
        return;
    }
    if (window?.matchMedia && window?.matchMedia('(prefers-color-scheme: dark)').matches) {
        setDarkMode(true);
    }else{
        setDarkMode(false);
    }
}
const setDarkMode=(status)=>{
    document.body.setAttribute('data-bs-theme', (status?'dark':'light'));
    app_data.darkMode=status;
}
const translate={};
const newToast = useToast();

const appbdURLs={
    route: function (route, param='',has_pre=true) {
        let actionStr = '';
        if (has_pre)
        {
            actionStr=settings.api_url+ route;
        }else {
            actionStr=route;
        }
        if (param!='')
        {
            actionStr +='/'+param;
        }
        actionStr = actionStr.toLowerCase().replace(/_/g, "-");

        return actionStr;
    },

    wp_plugin: function (action) {
        let actionStr = settings.base_slug + '-' + action;
        actionStr = actionStr.toLowerCase().replace("_", "-");
        return  settings.ajax_url + '&action=' + actionStr;
    },
    wp_module_url: function (module_id, action) {
        let actionStr = settings.base_slug + '-m-' + module_id + '-' + action;
        actionStr = actionStr.toLowerCase().replace(/_/g, "-");
        return settings.ajax_url + '&action=' + actionStr;
    },
}
const AppsbdUtls={
    getAppLogo() {
        try {
            return vitePos.app_logo;
        } catch (e) {
            return "logo.svg";
        }
    },
    toggleDarkMode(){
        if(app_data.darkMode){
            //disable
            sessionStorage.setItem('apbd_dark','N');
            setDarkMode(false);
        }else{
            //enable
            sessionStorage.setItem('apbd_dark','Y');
            setDarkMode(true);
        }
    },
    getFileInfo: (file) => {
        let ext = file.name.split('.').pop();
        ext = ext.toLowerCase();
        let fileInfo = AppsbdUtls.getFileIconByExt(ext, file.type);
        file.isImage = fileInfo.isImage;
        file.fileIcon = fileInfo.fileIcon;
        if ((file.size / 1048576) > 2.0) {
            /* setNotification({
                 "message": AppsbdUtls.translateGettext("File size is larger then %{allowed_size}", {allowed_size: '2MB'}),
                 "type": "alert",
             });*/
            return null;
        }
        return file;
    },
    getFileIconByExt: (ext, type) => {
        ext = ext.toLowerCase();
        let file = { isImage: false, fileIcon: 'apw apw-file-o' }
        if (type.substr(0, 3) == "ima") {
            file.isImage = true;
        } else if (ext == "pdf") {
            file.fileIcon = 'apw apw-file-pdf';
        } else if (ext == "zip") {
            file.fileIcon = 'apw apw-file-zip-o';
        } else if (ext == "doc" || ext == "docx") {
            file.fileIcon = 'apw apw-file-word';
        } else if (ext == "xls" || ext == "xlsx") {
            file.fileIcon = 'apw apw-file-excel';
        } else if (ext == "ppt" || ext == "pptx") {
            file.fileIcon = 'apw apw-file-powerpoint';
        } else if (ext == "mp4" || ext == "mpeg" || ext == "mkv" || ext == "avi") {
            file.fileIcon = 'apw apw-file-movie';
        }
        return file;
    },
    getUploadedFile: (file) => {
        let fileInfo = AppsbdUtls.getFileIconByExt(file.ext, file.type);
        return { ...file, ...{ name: AppsbdUtls.basename(file.url) }, ...fileInfo };
    },
    basename: function(path) {
        return path.split('/').reverse()[0];
    },
    bytesToSize: function(bytes) {
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
        if (bytes === 0) return 'n/a'
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10)
        if (i === 0) return `${bytes} ${sizes[i]}`
        return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`
    },

    ShowConfirmRequest(msg, callBack, props, error_callback) {
        var cData = {
            title: "",
            // html: AppsbdUtls.translateGettext(msg),
            text: msg,
            type: "warning",
            icon: "warning",
            timer: 3000,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#02cc1b',
            confirmButtonText: AppsbdUtls.translateGettext("Delete"),
            cancelButtonText: AppsbdUtls.translateGettext("Cancel"),
            showLoaderOnConfirm: true,
            // showDenyButton: false,
            preConfirm: function() {
                return new Promise(async (resolve, reject) => {
                    let response = await callBack();
                    if (!response.status) {
                        return reject(AppsbdUtls.GetErrorString(response, 'and'), null);
                    } else {
                        return resolve({
                            status: true,
                            msg: AppsbdUtls.GetInfoString(response, 'and')
                        });

                    }
                }).catch((error) => {
                    let errorMsg = "";
                    try {
                        errorMsg = error.toString();
                    } catch (e) {
                        errorMsg = AppsbdUtls.translateGettext("Unknown error");
                    }
                    Swal.showValidationMessage(
                        AppsbdUtls.translateGettext('Request failed: %{errorMsg}', { errorMsg: errorMsg })
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        };
        if (props && (typeof props == 'object')) {
            cData = { ...cData, ...props }
        }
        Swal.fire(cData).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({
                    type: "success",
                    icon: "success",
                    title: result.value.msg,
                    confirmButtonColor: '#02cc1b',
                    timer: cData.timer,
                });
            } else {
                if (typeof error_callback == "function") {
                    error_callback(result);
                }
            }
        });
    },
    GetErrorString(response, joinStr) {
        try {
            if (!joinStr) {
                joinStr = ',';
            } else {
                joinStr = AppsbdUtls.translateGettext(joinStr);
            }
            return response.msg.error.join(joinStr)
        } catch (e) {
            return "";
        }
    },
    GetInfoString(response, joinStr) {
        try {
            if (!joinStr) {
                joinStr = ',';
            } else {
                joinStr = AppsbdUtls.translateGettext(joinStr);
            }
            return response.msg.info.join(joinStr)
        } catch (e) {
            return "";
        }
    },
    ConfirmDialog(msg, callback, params, props, error_callback) {
        var thisObj = this;
        AppsbdUtls.ShowConfirmRequest(msg, function() {
            return callback(params, props, error_callback);
        });
    },
    changedFormData(addProps, currentProps) {
        return Object.keys(addProps).reduce((formData, field) => {
            if (addProps[field] !== currentProps[field]) {
                formData[field] = addProps[field];
            }
            return formData;
        }, {});
    },

    ShowNotification(msg, status, time_in_ms, position) {

        if (typeof status != "boolean") {
            if (!status) {
                newToast.error("My toast content", {
                    timeout: time_in_ms
                });
                return;
            }
        }
        newToast.success(msg, {
            timeout: time_in_ms,
            position: position
        });
    },
    ShowServerResponseNotification(msgs, time_in_ms, options) {
        if (!options) {
            options = {};
        }
        let opt = {
            ...{
                timeout: time_in_ms,
                position: ToastPosition.BOTTOM_RIGHT
            }, ...options
        }
        try {
            msgs.info.forEach(function(msg, index) {
                newToast.success(msg, opt);
            });
        } catch (e) {
        }
        try {
            msgs.error.forEach(function(msg, index) {
                newToast.error(msg, opt);
            });

        } catch (e) {
            newToast.warning(e.message, opt);
        }
    },
    translateGettext : (msg,params) =>{
        return  translate.gettext.translateGettext(msg,params);
    },

    translateGetMsg: (msg,params) =>{
        return  translate.gettext.translateGetMsg(msg,params);
    },
    AddLoadingClass(elem, status) {
        try {
            if (status) {
                elem.$el.classList.add('apbd-form-sending');
            } else {
                elem.$el.classList.remove('apbd-form-sending');
            }
        } catch (e) {

        }
    },

    imgCropController : function(attachment, controller) {

        var control = controller.get('control');

        var flexWidth = !!parseInt(control.params.flex_width, 10);
        var flexHeight = !!parseInt(control.params.flex_height, 10);

        var realWidth = attachment.get('width');
        var realHeight = attachment.get('height');

        var xInit = parseInt(control.params.width, 10);
        var yInit = parseInt(control.params.height, 10);

        var ratio = xInit / yInit;

        controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

        var xImg = xInit;
        var yImg = yInit;

        if (realWidth / realHeight > ratio) {
            yInit = realHeight;
            xInit = yInit * ratio;
        } else {
            xInit = realWidth;
            yInit = xInit / ratio;
        }

        var x1 = (realWidth - xInit) / 2;
        var y1 = (realHeight - yInit) / 2;

        var imgSelectOptions = {
            handles: true,
            keys: true,
            instance: true,
            persistent: true,
            imageWidth: realWidth,
            imageHeight: realHeight,
            minWidth: xImg > xInit ? xInit : xImg,
            minHeight: yImg > yInit ? yInit : yImg,
            x1: x1,
            y1: y1,
            x2: xInit + x1,
            y2: yInit + y1
        };

        return imgSelectOptions;
    },


    AppVersion: function() {
        return process.env.VUE_APP_VERSION;
    },
}


const AppsbdCore = {
    url:appbdURLs,
    utls:AppsbdUtls,
    AppData:app_data,
    install(Vue,translate) {
        Vue.config.globalProperties.$appsbdURL = AppsbdURL;
    },
}

const createAppsbdCore = (props) => {
    Object.assign(settings, props);
    setBrowserDarkStatus();
    return AppsbdCore;
};

export {createAppsbdCore}
export default AppsbdCore;
