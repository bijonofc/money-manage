import{useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";

const ACL = {
    checkACL: (action_param) => {
        const store = useLoginStore();
        if (!store.isLoggedIn || !store.loggedUserData?.caps) {
            return false;
        }
        if (store.loggedUserData.is_super === 'Y' || store.loggedUserData.caps['*']) {
            return true;
        }
        if (store.loggedUserData.caps[action_param]) {
            return true;
        }
        const cleanParam = action_param.replace(/^np\./, '');
        return !!(store.loggedUserData.caps[cleanParam]);
    },
    checkACLs: (...action_param) => {
      return ACL.checkACLsArray(action_param);
    },
    checkACLsArray: (action_param) => {
        for(let i in action_param){
            if(ACL.checkACL(action_param[i])){
                return true;
            }
        }
        return false;
    },
    install(Vue) {
        //console.log(store);
        Vue.config.globalProperties.$CheckACL = ACL.checkACL;
        Vue.config.globalProperties.$CheckACLS = ACL.checkACLs;
    },
}
export default ACL;
