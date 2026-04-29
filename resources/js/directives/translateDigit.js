const translateDigit = (str)=>{
    console.log("Directive called translateDigit");
    console.log(str);
    return str.toString().translate_digits();
}
const dir_digits = {
    mounted(el) {
        el.innerHTML = translateDigit(el.innerHTML)
    },
    updated(el) {
        el.innerHTML = translateDigit(el.innerHTML)
    }
};
export default {
    install(app) {
        console.log("Install called");
        console.log(app);
        app.directive('translate-digit', dir_digits);
    },
}
