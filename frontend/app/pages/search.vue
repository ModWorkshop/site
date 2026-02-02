<template>
	<page-block>
		<m-nav root="/search" :background="false">
			<m-nav-link to="mods" :title="$t('mods')"/>
			<m-nav-link to="games" :title="$t('games')"/>
			<m-nav-link to="threads" :title="$t('threads')"/>
			<m-nav-link to="users" :title="$t('users')"/>
			<template #content>
				<NuxtPage keepalive/>
			</template>
		</m-nav>
	</page-block>
</template>
