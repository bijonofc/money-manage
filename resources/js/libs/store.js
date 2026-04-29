import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

const pinia = createPinia();

// Use plugin **once**
pinia.use(piniaPluginPersistedstate);

export default pinia;


