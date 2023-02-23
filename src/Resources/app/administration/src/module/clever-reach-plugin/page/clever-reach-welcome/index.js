import template from './cleverreach-welcome.html.twig';
import './cleverreach-welcome.scss';

const {Component} = Shopware;

Component.register('cleverreach-welcome', {
    template,

    inject: [
        'cleverreachService'
    ],

    data() {
        return {
            isLoading: true,
            popup: null,
            currentTab: null
        };
    },

    mounted() {
        this.checkIfInitialSyncInProgress();
    },

    methods: {
        startAuthorizationProcess: function () {
            this.cleverreachService.getRedirectUrl().then((apiResponse) => {
                this.redirectToConnectionPopup(apiResponse.redirectUrl)
            }).catch(error => {
                console.log(error)
            });
        },

        redirectToConnectionPopup: function (redirectUrl) {
            this.isLoading = true;
            this.currentTab = window;
            this.popup = window.open(redirectUrl, '_blank');
            this.popup.focus();
            this.checkClientConnectionStatus();
        },

        checkClientConnectionStatus: function () {
            this.cleverreachService.checkConnectionStatus()
                .then((apiResponse) => {
                    if (apiResponse.isConnected) {
                        this.popup.close();
                        this.currentTab.focus();
                        window.location.reload();
                    } else {
                        let handler = this.checkClientConnectionStatus;
                        setTimeout(handler, 250);
                    }
                }).catch(error => {
                console.log(error);
            });
        },

        checkIfInitialSyncInProgress: function () {
            this.cleverreachService.checkIfInitialSyncInProgress()
                .then((apiResponse) => {
                    if (apiResponse.inProgress === false) {
                        this.isLoading = false;
                    }
                }).catch(error => {
                console.log(error);
            });
        }
    }
});