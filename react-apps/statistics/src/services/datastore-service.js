export default class DatastoreService {
    fetchMessages = () => {
        const request = `http://healthcheck.com/api/get/`;
        const params = this.createUrlParam('messages');
        return fetch(request + params);
    }

    fetchProjects = () => {
        const request = `http://healthcheck.com/api/get/`;
        const params = this.createUrlParam('projects');
        return fetch(request + params);
    }

    fetchLevels = () => {
        const request = `http://healthcheck.com/api/get/`;
        const params = this.createUrlParam('levels');
        return fetch(request + params);
    }

    getToken = () => {
        return this.getCoockie('token');
    }

    getCoockie = (name) => {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    getParams = () => {
        const paramsArray = window.location.search.substr(1).split('&');
        const params = {};
        for (let i=0; i < paramsArray.length; i++)
        {
            const param = paramsArray[i].split('=');
            if (param.length == 2) {
                params[param[0]] = param[1];
            }
        }

        return params;
    }

    createUrlParam = (entity) => {
        const token = this.getToken();
        const getParams = this.getParams();

        const page = getParams['page'] ? getParams['page'] : 1;
        const count = getParams['count'] ? getParams['count'] : 20;
        const params = `${token}/${entity}/${page}/${count}`;
        return params;
    }
}