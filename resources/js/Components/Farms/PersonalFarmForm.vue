<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import LocationSelect from '../Forms/LocationSelect.vue';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    farm: { type: Object, default: null },
    formTitle: { type: String, default: 'Add New Farm' },
    countries: { type: Array, default: () => [] },
    farmTypes: { type: Array, default: () => [] },
    farmSubtypes: { type: Array, default: () => [] },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const isCreate = computed(() => props.farm === null);
const farmTypes = ref([...(props.farmTypes ?? [])]);
const farmSubtypes = ref([...(props.farmSubtypes ?? [])]);

watch(() => props.farmTypes, (value) => { farmTypes.value = [...(value ?? [])]; });
watch(() => props.farmSubtypes, (value) => { farmSubtypes.value = [...(value ?? [])]; });

const lookupLists = {
    farm_types: farmTypes,
    farm_subtypes: farmSubtypes,
};
const lookupForm = useForm({ tag_name: '', name: '' });
const lookupOpen = ref(false);
const lookupSelectField = ref('');

const form = useForm({
    farm_type_id: props.farm?.farm_type_id ?? '',
    farm_subtype_id: props.farm?.farm_subtype_id ?? '',
    farm_name: props.farm?.farm_name ?? '',
    farm_noc: props.farm?.farm_noc ?? '',
    farm_area: props.farm?.farm_area ?? '',
    farm_capacity: props.farm?.farm_capacity ?? '',
    feed_room_size: props.farm?.feed_room_size ?? '',
    farm_address: props.farm?.farm_address ?? '',
    country_id: props.farm?.country_id ?? '',
    province_id: props.farm?.province_id ?? '',
    city_id: props.farm?.city_id ?? '',
    farm_image: null,
});

function openLookup(table, selectField) {
    lookupForm.reset();
    lookupForm.clearErrors();
    lookupForm.tag_name = table;
    lookupSelectField.value = selectField;
    lookupOpen.value = true;
}

function submitLookup() {
    lookupForm.post(route('inertia.farm-lookup-types.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const created = page.props.flash?.createdLookup;
            if (created?.id) {
                const list = lookupLists[created.table];
                if (list && ! list.value.some((item) => Number(item.id) === Number(created.id))) {
                    list.value = [...list.value, { id: created.id, name: created.name }];
                }
                if (lookupSelectField.value) {
                    form[lookupSelectField.value] = created.id;
                }
            }
            lookupOpen.value = false;
        },
    });
}

function submit() {
    form.submit(props.method, props.submitUrl, { forceFormData: true });
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6 align-self-start">
                <h4>{{ formTitle }}</h4>
            </div>
            <div class="col-6 align-self-end text-end mb-2">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-3 mb-2">
                    <label class="font_bold" for="CustomerFarmType">Select Farm Type *</label>
                    <div class="input-group">
                        <select id="CustomerFarmType" v-model="form.farm_type_id" class="form-control">
                            <option value="">Select Farm Type</option>
                            <option v-for="item in farmTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('farm_types', 'farm_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.farm_type_id" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartyFarmSubtype">Select Farm Subtype *</label>
                    <div class="input-group">
                        <select id="PartyFarmSubtype" v-model="form.farm_subtype_id" class="form-control">
                            <option value="">Select Farm subtype</option>
                            <option v-for="item in farmSubtypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('farm_subtypes', 'farm_subtype_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.farm_subtype_id" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="FarmName">Farm Name *</label>
                    <input id="FarmName" v-model="form.farm_name" class="form-control" type="text" placeholder="Enter name">
                    <FormError :message="form.errors.farm_name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="FarmNoc">Farm Noc *</label>
                    <input id="FarmNoc" v-model="form.farm_noc" class="form-control" type="text" placeholder="Enter Noc Number">
                    <FormError :message="form.errors.farm_noc" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="shedArea">Area</label>
                    <input id="shedArea" v-model="form.farm_area" class="form-control" type="number" min="0" step="any" placeholder="Enter Area">
                    <FormError :message="form.errors.farm_area" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="ShedCapacity">Capacity *</label>
                    <input id="ShedCapacity" v-model="form.farm_capacity" class="form-control" type="number" min="0" step="any" placeholder="Enter Capacity">
                    <FormError :message="form.errors.farm_capacity" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="FeedRoomSize">Feed Room Size *</label>
                    <input id="FeedRoomSize" v-model="form.feed_room_size" class="form-control" type="number" min="0" step="any" placeholder="Enter Room size">
                    <FormError :message="form.errors.feed_room_size" />
                </div>
                <LocationSelect
                    :countries="countries"
                    :country-id="form.country_id"
                    :province-id="form.province_id"
                    :city-id="form.city_id"
                    :errors="form.errors"
                    @update:country-id="form.country_id = $event"
                    @update:province-id="form.province_id = $event"
                    @update:city-id="form.city_id = $event"
                />
                <div class="col-3 mb-2">
                    <label class="font_bold" for="FarmImage">Farm Image *</label>
                    <input id="FarmImage" class="form-control" type="file" @change="form.farm_image = $event.target.files[0] ?? null">
                    <small v-if="! isCreate && farm?.farm_image" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.farm_image" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="FarmAddress">Farm Address</label>
                    <input id="FarmAddress" v-model="form.farm_address" class="form-control" type="text" placeholder="Enter Address">
                    <FormError :message="form.errors.farm_address" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                    <Link :href="indexUrl" class="ms-1">
                        <SecondaryButton type="button">Cancel</SecondaryButton>
                    </Link>
                </div>
            </div>
        </form>
        <FormModal :show="lookupOpen" title="Add new" size="md" @close="lookupOpen = false">
            <form autocomplete="off" @submit.prevent="submitLookup">
                <div class="mb-2">
                    <label for="TypeName">Name *</label>
                    <input id="TypeName" v-model="lookupForm.name" class="form-control" type="text" placeholder="Enter name">
                    <FormError :message="lookupForm.errors.name" />
                </div>
                <PrimaryButton type="submit" :disabled="lookupForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="lookupOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
    </div>
</template>
