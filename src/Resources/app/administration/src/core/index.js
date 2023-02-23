import CleverReachApiService from './service/cleverreach-api.service';

const initContainer = Shopware.Application.getContainer('init');

Shopware.Application.addServiceProvider('cleverreachService', (container) => {
    return new CleverReachApiService(initContainer.httpClient, container.loginService);
});