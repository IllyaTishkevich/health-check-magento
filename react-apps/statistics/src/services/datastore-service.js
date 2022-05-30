export default class DatastoreService {
    SITEURL = `http://healthcheck.com`;

    fetchMessages = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('messages');
        return fetch(request + params);
    }

    fetchProjects = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('projects');
        return fetch(request + params);
    }

    fetchLevels = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('levels');
        return fetch(request + params);
    }

    getToken = () => {
        return localStorage.getItem('token');
        // return this.getCoockie('token');
    }

    fetchStat = (level) => {
        const params = this.getParams();
        let date = [];
        if (params['filter.date']) {
            date = params['filter.date'].split('_');
        } else {
            const now = new Date();
            date[0] = `${now.getTime()}`;
            date[1] = `${now.setDate(now.getDate() - 1)}`;
        }
        const request = `${this.SITEURL}/api/stat/`;
        const step = params['step'] !== undefined ? `?step=${params['step']}` : '';
        const query = `${this.getToken()}/${level}/${date[0].slice(0, -3)}/${date[1].slice(0, -3)}${step}`;
        return fetch(request + query);
    }

    fetchMessageStat = (id) => {
        const params = this.getParams();
        let date = [];
        if (params['filter.date']) {
            date = params['filter.date'].split('_');
        } else {
            const now = new Date();
            date[0] = `${now.getTime()}`;
            date[1] = `${now.setDate(now.getDate() - 1)}`;
        }
        const request = `${this.SITEURL}/api/message/stat/`;
        const step = params['step'] !== undefined ? `?step=${params['step']}` : '';
        const query = `${id}/${this.getToken()}/${date[0].slice(0, -3)}/${date[1].slice(0, -3)}${step}`;
        return fetch(request + query);
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
        let params = `${token}/${entity}?page=${page}&count=${count}`;
        if (getParams['filter.level'] !== undefined) {
            params += `&level=${getParams['filter.level']}`
        }
        if (getParams['filter.date'] !== undefined) {
            const date = getParams['filter.date'].split('_');
            params += `&from=${date[0].slice(0, -3)}&to=${date[1].slice(0, -3)}`
        }
        if (getParams['filter.id'] !== undefined) {
            params += `&id=${getParams['filter.id']}`
        }
        if (getParams['filter.ip'] !== undefined) {
            params += `&ip=${getParams['filter.ip']}`
        }
        if (getParams['filter.message'] !== undefined) {
            params += `&message=${getParams['filter.message']}`
        }
        return params;
    }
}