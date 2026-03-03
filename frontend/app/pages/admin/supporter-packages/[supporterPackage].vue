<template>
	<simple-resource-form v-model="supporterPackage" url="supporter-packages" delete-button :redirect-to="getAdminUrl('supporter-packages')">
		<m-input v-model="supporterPackage.name" :label="$t('name')"/>
		<m-input v-model="supporterPackage.package_id" label="Package ID" desc="The package ID (On the provider)"/>
		<m-input v-model="supporterPackage.enabled" label="Enabled" type="checkbox"/>
		<m-input v-model="supporterPackage.order" label="Order" type="number"/>
		<m-input v-model="supporterPackage.price" label="Price" desc="The price in Euros of the package" type="number"/>
		<m-select v-model="supporterPackage.duration_type" label="Duration Type" :options="['mo', 'y', 'd']"/>
		<m-input v-model="supporterPackage.duration_number" label="Duration Number" type="number"/>
	</simple-resource-form>
</template>

<script setup lang="ts">
import type { SupporterPackage } from '~/types/models';

useNeedsPermission('admin');

const { data: supporterPackage } = await useEditResource<SupporterPackage>('supporterPackage', 'supporter-packages', {
	id: 0,
	enabled: true,
	order: 0,
	name: 'Supporter Package',
	package_id: 0,
	price: 3,
	duration_type: 'mo',
	duration_number: 1
});
</script>
