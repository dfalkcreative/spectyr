<template>
    <md-app md-mode="reveal">
        <md-app-toolbar class="md-primary">
            <md-button class="md-icon-button" @click="isMenuVisible = !isMenuVisible">
                <md-icon>menu</md-icon>
            </md-button>
            <span class="md-title">Sound Editor</span>
        </md-app-toolbar>

        <md-app-drawer :md-active.sync="isMenuVisible">
            <md-toolbar class="md-transparent" md-elevation="0">Navigation</md-toolbar>

            <md-list>
                <md-list-item>
                    <md-icon>move_to_inbox</md-icon>
                    <span class="md-list-item-text">Inbox</span>
                </md-list-item>

                <md-list-item>
                    <md-icon>send</md-icon>
                    <span class="md-list-item-text">Sent Mail</span>
                </md-list-item>

                <md-list-item>
                    <md-icon>delete</md-icon>
                    <span class="md-list-item-text">Trash</span>
                </md-list-item>

                <md-list-item>
                    <md-icon>error</md-icon>
                    <span class="md-list-item-text">Spam</span>
                </md-list-item>
            </md-list>
        </md-app-drawer>

        <md-app-content>
            <slot name="body"></slot>
        </md-app-content>
    </md-app>
</template>
<script>
    export default {
        data(){
            return {
                isMenuVisible: false
            }
        }
    }
</script>
<style>
    .md-app{
        min-height: 100vh;
    }

    .d-flex-justify{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>