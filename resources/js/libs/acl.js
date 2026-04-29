import{useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";

const ACL = {
    checkACL: (action_param) => {
        const store = useLoginStore();
        if (window.appType=='agent')
        {
            const store = useAgentLoginStore();
        }
        return store.isLoggedIn && store.loggedUserData.caps?.[action_param];
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
