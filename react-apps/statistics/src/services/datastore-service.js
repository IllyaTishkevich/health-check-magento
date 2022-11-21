import { getGmt } from "../setting-action";

export default class DatastoreService {
    SITEURL = location.origin;
    // SITEURL = `http://healthcheck.com`;
    // SITEURL = 'http://healthcheck.relikt.monster';

    fetchMessages = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('messages');
        return fetch(request + params);
    }

    fetchNotifications = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('notifications');
        return fetch(request + params);
    }

    fetchSenders = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('senders');
        return fetch(request + params);
    }

    fetchProject = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('project');
        return fetch(request + params);
    }

    fetchUsers = () => {
        const request = `${this.SITEURL}/api/get/`;
        const params = this.createUrlParam('users');
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
        const from = this.createDateParam(date[0]);
        const to = this.createDateParam(date[1]);
        const request = `${this.SITEURL}/api/stat/`;
        const step = params['step'] !== undefined ? `?step=${params['step']}` : '';
        const query = `${this.getToken()}/${level}/${from}/${to}${step}`;
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

        const from = this.createDateParam(date[0]);
        const to = this.createDateParam(date[1]);

        const request = `${this.SITEURL}/api/message/stat/`;
        const step = params['step'] !== undefined ? `?step=${params['step']}` : '';
        const query = `${id}/${this.getToken()}/${from}/${to}${step}`;
        return fetch(request + query);
    }

    saveNotification = (data) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/notification/set/${token}`;
        return fetch(request, {
            method: 'POST',
            mode: 'no-cors',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
    }

    removeNotification = (id) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/notification/remove/${token}/${id}`;
        return fetch(request)
    }

    removeUser = (id) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/user/remove/${token}/${id}`;
        return fetch(request)
    }

    addUser = (email) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/user/add/${token}/${email}`;
        return fetch(request)
    }

    setGmt = (gmt) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/set/gmt/${token}/${gmt}`;
        return fetch(request)
    }

    setEnableServerCheck = (enableServerCheck) => {
        const token = this.getToken();
        const request = `${this.SITEURL}/api/set/enable_server_check/${token}/${enableServerCheck}`;
        return fetch(request)
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
            const from = this.createDateParam(date[0]);
            const to = this.createDateParam(date[1]);
            params += `&from=${from}&to=${to}`
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

    createDateParam = (timestamp) => {
        const date = new Date(Number(timestamp));
        return `${date.getFullYear()}d${String(date.getMonth() + 1).padStart(2, '0')}d${String(date.getDate()).padStart(2, '0')}T${String(date.getHours()).padStart(2, '0')}p${String(date.getMinutes()).padStart(2, '0')}p${String(date.getSeconds()).padStart(2, '0')}`;
    }
}