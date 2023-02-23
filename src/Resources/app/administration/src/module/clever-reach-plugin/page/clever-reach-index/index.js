import template from './cleverreach-index.html.twig';
import "./cleverreach-index.scss";

const {Component} = Shopware;

Component.register('cleverreach-index', {
    template,

    inject: [
        'cleverreachService'
    ],

    data() {
        return {
            isLoading: true
        };
    },

    mounted: function () {
        this.getCurrentRoute({});
    },

    watch: {
        $route(to, from) {
            let query = {};

            if (to.hasOwnProperty('query') && Object.keys(to.query).length > 0) {
                query = to.query;
            } else if (from.hasOwnProperty('query') && Object.keys(from.query).length > 0) {
                query = from.query;
            }

            this.getCurrentRoute(query);
        }
    },

    methods: {
        getCurrentRoute: function (query) {
            this.cleverreachService.getCurrentRoute()
                .then((response) => {
                    this.isLoading = false;
                    let routeName = response.page;
                    let route = {
                        name: 'cleverreach.plugin.index',
                        params: {
                            page: routeName
                        },
                        query: query
                    };

                    this.$router.replace(route);
                    this.isLoading = false;
            }).catch(error => {
                console.log(error);
            });
        }
    }
});
