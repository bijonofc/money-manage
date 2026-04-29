export default {
    install: (app, options) => {
        // Add a global property accessible in setup()
        app.config.globalProperties.$rootData = options;

        // Optional: provide it so you can inject it in setup()
        app.provide('rootData', options);
    }
};
