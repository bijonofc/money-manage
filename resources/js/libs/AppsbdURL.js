const settings={
    base_slug:'',
    ajax_url:null,
    api_url:'/',
    base_url:'/',
}

const AppsbdURL = {
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
    install(Vue,translate) {
        Vue.config.globalProperties.$appsbdURL = AppsbdURL;
    },
}
const createAppsbdURL = (props) => {
    Object.assign(settings, props);
    return AppsbdURL;
};

export {createAppsbdURL}
export default AppsbdURL;
