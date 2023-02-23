import template from './cleverreach-dashboard.html.twig';
import './cleverreach-dashboard.scss';

const {Component} = Shopware;

Component.register('cleverreach-dashboard', {
    template,

    inject: [
        'cleverreachService'
    ],

    data() {
        return {
            isLoading: true,
            status: "progress",
            clientId: "ABC123"
        };
    },

    mounted() {
        this.fetchClientId();
        this.checkClientConnectionStatus();
    },

    methods: {
        fetchClientId: function () {
            this.cleverreachService.getClientId()
                .then((apiResponse) => {
                    this.clientId = apiResponse.clientId;
                }).catch(error => {
                console.log(error);
            });
        },

        startSynchronizationProcess: function () {
            this.status = "progress";
            this.cleverreachService.startManualSync().catch(error => {
                console.log(error)
            });
            this.checkManualSyncStatus();
        },

        checkManualSyncStatus: function () {
            this.cleverreachService.checkConnectionStatus()
                .then((apiResponse) => {
                    if (apiResponse.isConnected) {
                        this.status = "done";
                    } else {
                        let handler = this.checkManualSyncStatus;
                        setTimeout(handler, 250);
                    }
                }).catch(error => {
                console.log(error);
            });
        },

        checkClientConnectionStatus: function () {
            this.cleverreachService.checkConnectionStatus()
                .then((apiResponse) => {
                    if (apiResponse.isConnected) {
                        this.status = "done";
                        this.isLoading = false;
                    }
                }).catch(error => {
                console.log(error);
            });
        }
    }
});