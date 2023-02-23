const ApiService = Shopware.Classes.ApiService;

class CleverReachApiService extends ApiService {
    constructor(httpClient, loginService) {
        super(httpClient, loginService, 'cleverreach');
        this.name = 'cleverreachService';
    }

    getCurrentRoute() {
        return this.httpClient.get(
            '/cleverreach/router',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    checkConnectionStatus() {
        return this.httpClient.get(
            '/cleverreach/connectionStatus',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    checkIfInitialSyncInProgress() {
        return this.httpClient.get(
            '/cleverreach/initialSyncInProgress',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    getRedirectUrl() {
        return this.httpClient.get(
            '/cleverreach/redirectUrl',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    startManualSync() {
        return this.httpClient.get(
            '/cleverreach/manualSync',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    getClientId() {
        return this.httpClient.get(
            '/cleverreach/getClientId',
            {headers: this.getBasicHeaders()}
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }
}

export default CleverReachApiService;
