<template>
    <footer>
        MWS V3
        <div style="margin-left: auto;">
            <el-select v-model="$colorMode.preference">
                <el-option value="system">System</el-option>
                <el-option value="light">Light</el-option>
                <el-option value="dark">Dark</el-option>
            </el-select>
        </div>
    </footer>
</template>
<style>
    footer {
        padding: 1rem;
        display: flex;
        align-items: center;
        background-color: var(--header-footer-color);
        grid-area: footer;
    }
</style>