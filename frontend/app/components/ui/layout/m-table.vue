<template>
	<m-flex column class="table-container">
		<table :class="{ table: true, 'table-bg': background, 'alt-table-bg': altBackground, 'table-fixed': fixed }">
			<slot>
				<thead>
					<tr>
						<slot name="head"/>
					</tr>
				</thead>
				<tbody>
					<slot name="body">
						<tr>
							<td colspan="199" class="text-center">
								{{ $t('nothing_found') }}
							</td>
						</tr>
					</slot>
				</tbody>
			</slot>
		</table>
	</m-flex>
</template>

<script setup lang="ts">
const { background = true } = defineProps<{
	background?: boolean;
	altBackground?: boolean;
	fixed?: boolean;
}>();
</script>

<style scoped>
.table-container {
	overflow-x: auto;
}

.table {
	width: 100%;
}
</style>

<style>
.markdown table, .table {
	border-radius: var(--border-radius);
	border-spacing: 0rem 0.25rem;
	white-space: nowrap;
}

.markdown td, .table td {
	padding: 0.75rem;
}

.markdown th, .table th {
	padding: 1rem;
}

.markdown th, .table-bg th {
	background-color: var(--table-even-color);
}

.markdown th, .alt-table-bg th {
	background-color: var(--alt-table-even-color);
}

.table-bg td {
	background-color: var(--content-bg-color);
}

.table-bg tr:nth-child(2n + 1) td {
	background-color: var(--table-odd-color);
}

.table-bg tr:nth-child(2n) td {
	background-color: var(--table-even-color);
}

.markdown tr:nth-child(2n of tr) td, .alt-table-bg tr:nth-child(2n of tr) td {
	background-color: var(--alt-table-even-color);
}

.markdown tr:nth-child(2n+1 of tr) td, .alt-table-bg tr:nth-child(2n+1 of tr) td {
	background-color: var(--alt-table-odd-color);
}

.table th:first-child {
	border-top-left-radius: var(--content-border-radius);
}

.table th:last-child {
	border-top-right-radius: var(--content-border-radius);
}

.table tr td {
	border-bottom: 2px solid var(--content-bg-color);
}

.table tr:nth-last-child(1 of tr:not(.hidden)) td:first-child {
	border-bottom-left-radius: var(--content-border-radius);
}

.table tr:nth-last-child(1 of tr:not(.hidden)) td:last-child {
	border-bottom-right-radius: var(--content-border-radius);
}
</style>
