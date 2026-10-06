import { ref, watch } from "vue";

export default {
    install: (app) => {
        app.config.globalProperties.$useStorage = {
            cacheDatav1(key, value = null) {
                let storedValue = read(key);

                if (storedValue) {
                    value = ref(storedValue);
                } else {
                    value = ref(value);

                    write();
                }

                // watch(value, write());
                watch(value, write(), { deep: true });

                function read(key) {
                    return JSON.parse(localStorage.getItem(key));
                }

                function write() {
                    if (value.value === null || value.value === '') {
                        localStorage.removeItem(key);
                    } else {
                        localStorage.setItem(key, JSON.stringify(value.value));
                    }
                }

                return storedValue;
            },

            cacheDatav2(key, defaultValue = null) {
                // 1. Try to fetch existing value from LocalStorage
                const storedItem = localStorage.getItem(key);
                
                // 2. Initialize the ref
                // If storage exists, parse it. Otherwise, use the defaultValue passed in.
                let initialValue = defaultValue;
                if (storedItem) {
                    try {
                        initialValue = JSON.parse(storedItem);
                    } catch (e) {
                        console.warn(`[useStorage] Error parsing key "${key}":`, e);
                    }
                }

                const data = ref(initialValue);

                // 3. Define the write function
                function write() {
                    if (data.value === null || data.value === '') {
                        localStorage.removeItem(key);
                    } else {
                        localStorage.setItem(key, JSON.stringify(data.value));
                    }
                }

                // 4. If we used the default value (nothing in storage), write it now so storage syncs
                if (!storedItem) {
                    write();
                }

                // 5. Watch for changes (Deep watch ensures objects/arrays are tracked)
                // Note: We pass the function reference 'write', do not call it with 'write()'
                watch(data, write, { deep: true });

                // 6. Return the reactive ref so the component can use it
                return data;
            }
        }
    }
};
