//import { useNotificationStore } from '@dafcoe/vue-notification'
//const { setNotification } = useNotificationStore()
import Darkmode from 'darkmode-js';
import Swal from "sweetalert2";
import { useToast,POSITION as ToastPosition } from "vue-toastification";

import useBreakpoint from "@/libs/Responsive";
const options = {
    bottom: '64px', // default: '32px'
    right: 'unset', // default: '32px'
    left: '32px', // default: 'unset'
    time: '0.5s', // default: '0.3s'
    mixColor: '#fff', // default: '#fff'
    backgroundColor: '#fff',  // default: '#fff'
    buttonColorDark: '#100f2c',  // default: '#100f2c'
    buttonColorLight: '#fff', // default: '#fff'
    saveInCookies: false, // default: true,
    label: '🌓', // default: ''
    autoMatchOsTheme: true // default: true
}
const newToast = useToast();

const appDarkmode = new Darkmode(options);
//const { ScreenWidth, ScreenType } = useBreakpoint();
const translate={};

const AppsbdUtls =
    {
        getAppLogo() {
            try {
                return vitePos.app_logo;
            } catch (e) {
                return "logo.svg";
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
        NotificationPosition: ToastPosition,
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
                    AppsbdUtls.ShowNotificationbyType(msg,'s',opt,time_in_ms);
                });
            } catch (e) {
            }
            try {
                msgs.error.forEach(function(msg, index) {
                    AppsbdUtls.ShowNotificationbyType(msg,'e',opt,time_in_ms);
                });
            } catch (e) {
                AppsbdUtls.ShowNotificationbyType(e.message,'w',opt,time_in_ms);
            }
        },
        ShowNotificationbyType(msg, type='e', options,time_in_ms) {
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
                if(type=='e'){
                    newToast.error( this.translateGettext(msg), opt);
                }else if(type=='w'){
                    newToast.warning(this.translateGettext(msg), opt);
                }else if(type=='s'){
                    newToast.success(this.translateGettext(msg), opt);
                }else{
                    newToast.info(this.translateGettext(msg), opt);
                }
            } catch (e) {
                newToast.error(e.message, opt);
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


        getErrorMsg: (msg) => {
            if (msg != '') {
                /* setNotification({
                     "message": AppsbdUtls.translateGettext(msg),
                     "type": "alert",
                 });*/
                return null;
            }
        },
        ScreenWidth: function() {
            return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        },
        ScreenHeight: function() {
            return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        },
        IsExtraSmallDevice() {
            return AppsbdUtls.ScreenWidth() <= 576;
        },
        IsSmallDevice() {
            let w = AppsbdUtls.ScreenWidth();
            return w > 576 && w <= 768;
        },
        IsUptoSmallDevice() {
            return AppsbdUtls.ScreenWidth() <= 768;
        },
        IsMediumDevice() {
            let w = AppsbdUtls.ScreenWidth();
            return w > 786 && w <= 992;
        },
        IsUptoMediumDevice() {
            return AppsbdUtls.ScreenWidth() <= 992;
        },
        IsLargeDevice() {
            let w = AppsbdUtls.ScreenWidth();
            return w > 992 && w <= 1199;
        },
        IsUptoLargeDevice() {
            return AppsbdUtls.ScreenWidth() <= 1199;
        },
        IsExtraLargeDevice() {
            return AppsbdUtls.ScreenWidth() > 1199;
        },
        DarkmodeTaggle() {
            appDarkmode.toggle();
        },
        ChangeDarkmode(status) {
            let dStatus = appDarkmode.isActivated();
            if (status) {
                if (!dStatus) {
                    appDarkmode.toggle();
                }
            } else {
                if (dStatus) {
                    appDarkmode.toggle();
                }
            }
        },
        DarkmodeObject() {
            return appDarkmode;
        },
        markdownToHtml(text){
             // Parse headers (optional enhancement)
            text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');
            text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');

            // Parse bold text
            text = text.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');

            // Parse italic text
            text = text.replace(/\*(.*?)\*/gim, '<em>$1</em>');

            // Parse color tags
            text = text.replace(/\[color:(.*?)\](.*?)\[\/color\]/gim, '<span style="color: $1">$2</span>');

            // Parse line breaks
            text = text.replace(/\n/gim, '<br>');

            return text;
        },
        WPFileChooser: function(title, buttonText, callback, fileType, isSingle, onClose) {
            let params = {
                ...{
                    type: "",
                    title: "Image Chooser",
                    button_text: "Select",
                    multiple: false,
                    callback: function(selected) {
                    },
                    onClose: function() {
                    }
                }, ...args
            }

            if (typeof wp == "undefined" || !wp.media) {
                let testObj = {
                    "id": 3598,
                    "title": "w-logo-blue.png",
                    "filename": "w-logo-blue.png",
                    "url": "wp-admin/images/w-logo-blue.png"
                }
                params.callback(testObj);
                return;
            }
            params.title = AppsbdUtls.translateGettext(params.title);
            params.button_text = AppsbdUtls.translateGettext(params.button_text);

            let Uploader = wp.media({
                title: params.title,
                library: {
                    type: params.type
                },
                button: {
                    text: params.button_text,
                }, multiple: params.multiple
            }).on('select', function() {
                var attachment = Uploader.state().get('selection').first().toJSON();
                try {
                    params.callback(attachment);
                } catch (e) {
                    console.log(e.message);
                }
            }).on('close', function() {
                params.onClose();
            }).open();
        },
        POSLink: function() {
            try {
                return vitePos.pos_link;
            } catch (e) {
                return '';
            }
        },
        WPCR: function() {
            return atob('PGEgaHJlZj0iaHR0cHM6Ly92aXRlcG9zLmNvbSIgdGFyZ2V0PSJfYmxhbmsiPlZpdGVwb3M8L2E+LCBDb3B5cmlnaHQgqQ==') + (new Date().getFullYear()) + atob('IDxhIGhyZWY9Imh0dHBzOi8vYXBwc2JkLmNvbSIgdGFyZ2V0PSJfYmxhbmsiPkFwcHNiZDwvYT4uIEFsbCByaWdodHMgcmVzZXJ2ZWQu');
            //  return btoa('<a href="https://vitepos.com" target="_blank">Vitepos</a>, Copyright ©')+(new Date().getFullYear())+btoa(' <a href="https://appsbd.com" target="_blank">Appsbd</a>. All rights reserved.');
        },
        WPFOOTER: function() {
            return atob('R2VuZXJhdGVkIGJ5IDogVml0ZVBvcywgdmlzaXQ6IHZpdGVwb3MuY29t');
        },
        makeHtml:(text)=>{
            // Parse headers (optional enhancement)
            text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');
            text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');

            // Parse bold text
            text = text.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');

            // Parse italic text
            text = text.replace(/\*(.*?)\*/gim, '<em>$1</em>');

            // Parse color tags
            text = text.replace(/\[color:(.*?)\](.*?)\[\/color\]/gim, '<span style="color: $1">$2</span>');
            // Replace [br] in text to <br/>
            text = text.replace(/\[br\]/gim, '<br/>');

            // Parse line breaks
            text = text.replace(/\n/gim, '<br>');
            return text;
        },
        install(Vue, gettext) {
            translate.gettext=gettext;

            Vue.config.globalProperties.$appsbdUtls = AppsbdUtls;
            Vue.config.globalProperties.vitePos = window.vitePos;
            Vue.config.globalProperties.$translate=gettext;
            Vue.config.globalProperties.$translateGettext=gettext.translateGettext;
            Vue.config.globalProperties.$translateGetMsg=gettext.translateGetMsg;
            //Vue.provide('appsbdUtls', AppsbdUtls);
        },
    }
export default AppsbdUtls;
