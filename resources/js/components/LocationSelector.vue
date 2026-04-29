<template>
    <div class="row" :class="props.responsiveColumn">
        <div class="col">
            <label for="country_name" class="form-label" v-translate>gl.select.country</label>
            <Field v-model="props.modelValue.country" name="country">
                <multiselect
                    v-model="props.modelValue.country"
                    label="name"
                    @clear="props.modelValue.state = ''"
                    @change="onCountryChange"
                    valueProp="iso2"
                    id="country_name"
                    placeholder="Select/Search Country"
                    :searchable="true"
                    :options="countries"
                />
            </Field>
            <ErrorMessage name="country" class="apbd-v-error"/>
        </div>
        <div class="col">
            <label for="state" class="form-label" v-translate>gl.state.district</label>
            <Field v-model="props.modelValue.state" name="state">
                <multiselect
                    v-model="props.modelValue.state"
                    label="name"
                    valueProp="code"
                    id="state"
                    @clear="props.modelValue.city = ''"
                    placeholder="Select state/district"
                    :searchable="true"
                    :options="states"
                />
            </Field>
            <ErrorMessage name="state" class="apbd-v-error"/>
        </div>

        <div class="col">
            <div v-if="cities.length>0">
                <label for="city" class="form-label" v-translate>gbl.city</label>
                <Field v-model="props.modelValue.city" name="city">
                    <multiselect
                        v-model="props.modelValue.city"
                        label="name"
                        valueProp="name"
                        id="city"
                        placeholder="Select city"
                        :searchable="true"
                        :options="cities"
                    />
                </Field>
                <ErrorMessage name="city" class="apbd-v-error"/>
            </div>
            <input-field label="gbl.city" v-model="props.modelValue.city" v-else name="city"/>
        </div>
        <div class="col">
            <input-field label="gbl.zip" v-model="props.modelValue.zip_code" name="zip_code"/>
        </div>
        <div class="col">
            <apbd-radio-button label="gbl.status" v-model="props.modelValue.status" name="status" :options="options"/>
        </div>
        <div class="col">
            <label for="address" class="form-label" v-translate>gbl.address</label>
            <Field as="textarea" label="gbl.address" class="form-control form-control-sm" v-model="props.modelValue.address" name="address" style="height: 120px"/>
        </div>
    </div>
</template>

<script setup>
import { ref,computed, onMounted,watch } from 'vue';
import {ApbdRadioButton, InputField} from "@appsbd/vue3-appsbd-libs";
import {Field,ErrorMessage} from "vee-validate";
import Multiselect from "@vueform/multiselect";
import Countries from '@/libs/countries.json';

const props = defineProps({
    responsiveColumn: {
      type: String,
      default: 'row-cols-1 row-cols-sm-2 g-3'
    },
    modelValue: {
        type: Object,
        required: true
    }
})

const countries = ref(Countries);
const options = [
    { val: 'A', title: 'gbl.active' },
    { val: 'I', title: 'gbl.inactive' },
]

const states = computed(() => {
    const country = countries.value.find(
        c => c.iso2 === props.modelValue.country
    )
    return country ? country.states : []
})

const cities = computed(() => {
    if (!props.modelValue.state) return []

    const state = states.value.find(
        s => s.code === props.modelValue.state
    )

    return state ? state.cities : []
})



onMounted(()=>{

})

const onCountryChange = (value) => {
    if (value !== props.modelValue.country) {
        props.modelValue.state = '';
        props.modelValue.city = '';
    }
}
</script>

<style scoped lang="scss">

</style>
