<template>
    <div class="search-wrapper">
        <input v-model="objectToDisplay[fieldToDisplay]" :id="id" class="form-control" type="text" @keyup="onSearch()" @focus="isFocus=true" @blur="clickedOutside" :placeholder="placeholder" :required="required" :disabled="disabled">

        <div class="div-dropdown" v-if="isFocus">
            <ul class="ul-option">
                <li v-if="! isSearchDone" class="li-progress">
                    <div class="spinner-border text-info spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </li>
                <li class="li-noresult" v-if="! data.length > 0 && isSearchDone">
                    <div style="text-align: center;">No Result</div>
                </li>
                <li class="li-search" v-if="data.length > 0" v-for="(item, index) in data" :key="`item-${index}`" @click="onSelect(item)">
                    {{ displayTextFromSelectedItem(item) }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: {
            id: {
                type: String,
                required: false
            },
            apiPath: {
                type: String,
                required: true
            },
            placeholder: {
                type: String,
                required: false
            },
            attributeObjectToDisplayInTheList: {
                type: String,
                required: false,
            },
            fieldToDisplay: {
                type: String,
                required: true
            },
            limit: {
                type: Number,
                required: false,
                default: 15
            },
            dataSelector: {
                type: String,
                required: false,
                default: 'data'
            },
            fieldToUpdate: {
                type: String,
                required: true
            },
            fieldUsedToUpdate: {
                type: String,
                default: 'id',
                required: false
            },
            objectToUpdate: {
                type: [String, Object],
                required: false
            },
            arrayObject: {
                type: String,
                required: false,
            },
            arrayObjectIndex: {
                type: Number,
                required: false
            },
            selectedObject: {
                type: [Object, String],
                required: false
            },
            objectToDisplay: {
                type: Object,
                required: false,
                default: {}
            },
            disableFieldToDisplayAsParameter: {
                type: Boolean,
                required: false,
                default: false
            },
            displayFieldInNestedObject: {
                type: Boolean,
                required: false,
                default: false
            },
            additionalDynamicParameters: {
                type: Array,
                required: false,
                default: []
            },
            additionalParameters: {
                type: Object,
                required: false,
            },
            required: {
                type: Boolean,
                default: false,
                required: false
            },
            disabled: {
                type: Boolean,
                default: false,
                required: false
            },
            hasPivot: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                selectedItem:  {},
                data:          [],
                isFocus:       false,
                isSearchDone:  true
            }
        },

        watch: {
            objectToDisplay(newObjectToDisplay, oldObjectToDisplay) {
                this.updateObjectToDisplay(newObjectToDisplay);
            }
        },

        mounted() {
            if (this.displayFieldInNestedObject) {
            } else {
                this.updateObjectToDisplay(this.objectToDisplay);
            }
        },

        methods: {
            onSearch() {
                // if (this.objectToDisplay[this.fieldToDisplay] == '') {
                //     this.isSearchDone = false;
                //     return;
                // }

                this.isSearchDone = false;

                let parameters = {};

                if (! this.disableFieldToDisplayAsParameter) {
                    parameters[this.fieldToDisplay] = this.objectToDisplay[this.fieldToDisplay];
                }

                parameters = { ...parameters, ...this.additionalParameters };

                if (this.additionalDynamicParameters.length > 0) {
                    this.additionalDynamicParameters.forEach(additionalDynamicParameter => {
                        parameters[additionalDynamicParameter] = this.objectToDisplay[this.fieldToDisplay];
                    });
                }

                this.isSearchDone = false;
                this.search(parameters, this);
            },
            search: _.debounce((parameters, vm) => {
                axios.get(vm.apiPath, {
                    params: parameters
                }).then(res => {
                    vm.data = res.data[vm.dataSelector];
                    vm.isSearchDone = true;
                });
            }, 350),
            updateObjectToDisplay(objectToDisplay) {
                if (typeof objectToDisplay !== 'undefined' && objectToDisplay !== null && typeof objectToDisplay === 'object' && Object.keys(objectToDisplay).length > 0) {
                    this.selectedItem = objectToDisplay;
                }
            },
            onSelect(selectedItem) {
                if (typeof this.arrayObjectIndex !== 'undefined' && this.arrayObjectIndex !== null && typeof this.arrayObjectIndex === 'number' && this.arrayObject) {
                    if (typeof selectedItem !== 'undefined' && selectedItem !== null && typeof selectedItem === 'object' && Object.keys(selectedItem).length > 0) {
                        if (this.displayFieldInNestedObject) {
                            this.selectedItem = selectedItem;
                            this.$parent.$parent[this.arrayObject][this.arrayObjectIndex][this.selectedObject] = selectedItem;
                            this.$parent.$parent[this.arrayObject][this.arrayObjectIndex][this.selectedObject][this.fieldToUpdate] = selectedItem[this.fieldUsedToUpdate];
                        } else {
                            this.selectedItem = selectedItem;
                            this.$parent.$parent[this.arrayObject][this.arrayObjectIndex][this.selectedObject] = selectedItem;
                            this.$parent.$parent[this.arrayObject][this.arrayObjectIndex][this.fieldToUpdate] = selectedItem[this.fieldUsedToUpdate];
                        }

                        this.$emit('SelectOption', selectedItem);
                    } else {
                        this.selectedItem = this.objectToDisplay;
                        this.$emit('SelectOption', this.objectToDisplay);
                    }
                } else if (typeof selectedItem === 'object') {
                    this.selectedItem = selectedItem;
                    this.$parent.$parent[this.objectToUpdate] = selectedItem;
                    this.$parent.$parent[this.fieldToUpdate] = selectedItem[this.fieldUsedToUpdate];

                    this.$parent[this.objectToUpdate] = selectedItem;
                    this.$parent[this.fieldToUpdate] = selectedItem[this.fieldUsedToUpdate];
                    this.$emit('SelectOption', selectedItem);
                }
            },
            displayTextFromSelectedItem(item) {
                if (this.displayFieldInNestedObject) {
                    return item[this.attributeObjectToDisplayInTheList][this.fieldToDisplay];
                } else {
                    return item[this.fieldToDisplay];
                }
            },
            clickedOutside() {
                if (this.data.length > 0) {
                    setTimeout(() => this.isFocus = false, 250);
                } else {
                    this.isFocus = false;
                }
            }
        }
    }
</script>

<style>
    .input-group > .search-wrapper {
        width: 0;
        flex: 1 1 auto;
    }

    .input-group > .search-wrapper > .form-control {
        margin-left: calc(-1 * var(--bs-border-width));
        border-bottom-left-radius: 0 !important;
        border-top-left-radius: 0 !important;
    }

    .search-wrapper {
        flex-direction: column;
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .div-dropdown {
        position: absolute;
        width: 100%;
        border: 1px solid lightgray;
        border-radius: 5px;
        background-color: white;
        z-index: 1000;
    }
    .ul-option {
        list-style: none;
        text-align: left;
        padding: 8px;
        max-height: 250px;
        overflow-y: scroll;
        overflow-x: hidden;
        z-index: 1000;
        background-color: white;
        margin-bottom: 10px;
    }
    .li-search {
        padding: 5px;
        background-color: white;
        cursor: pointer;
    }
    .li-search:hover {
        background-color: whitesmoke;
        font-weight: bold;
        border-radius: 5px;
    }
    .li-progress{
        margin-top: 5px;
        margin-bottom: -5px;
    }
    .li-noresult{
        margin-top: 5px;
        margin-bottom: -10px;
    }
</style>
