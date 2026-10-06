import { ref, watch } from "vue";

export function useLocalStorage(key, defaultValue = null) {
    // 1. Try to fetch existing value from LocalStorage
    const storedItem = localStorage.getItem(key);
    
    // 2. Initialize the ref
    let initialValue = defaultValue;

    if (storedItem !== null) {
        if (storedItem === 'undefined') {
            initialValue = defaultValue;
        } else if (storedItem === '') {
            initialValue = '';
        } else if (/^-?\d+$/.test(storedItem)) {
            // intercept and explicitly parse integers (e.g., "42", "-7", "0")
            // This safely handles both JSON numbers and unquoted primitive numbers
            initialValue = parseInt(storedItem, 10);
        } else {
            try {
                initialValue = JSON.parse(storedItem);
            } catch (e) {
                // Fallback for unquoted string primitives (e.g., "hello")
                initialValue = storedItem;
            }
        }
    }

    const data = ref(initialValue);

    // 3. Define the write function
    function write() {
        // Strict checks ensure that the integer 0 is NOT cleared (since 0 !== '')
        if (data.value === null || data.value === undefined || data.value === '') {
            localStorage.removeItem(key);
        } else {
            localStorage.setItem(key, JSON.stringify(data.value));
        }
    }

    // 4. If nothing was in storage, write the default value to sync it
    if (storedItem === null) {
        write();
    }

    // 5. Watch for changes
    watch(data, write, { deep: true });

    // 6. Return the reactive ref
    return data;
}
