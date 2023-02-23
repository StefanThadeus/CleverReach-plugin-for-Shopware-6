import './page/clever-reach-index';
import './page/clever-reach-welcome';
import './page/clever-reach-dashboard';

Shopware.Module.register('cleverreach-plugin', {
    type: 'plugin',
    name: 'clever-reach.basic.label',
    title: 'clever-reach.basic.label',
    description: 'clever-reach.basic.description',

    routes: {
        index: {
            component: 'cleverreach-index',
            path: ':page?'
        }
    },

    navigation: [{
        label: 'CleverReach',
        color: '#ff3d58',
        path: 'cleverreach.plugin.index',
        icon: 'default-shopping-paper-bag-product',
        position: 100,
        parent: 'sw-marketing'
    }]
});