"use strict";

class TrackedConsole {
    original = null;
    tracker = null;

    constructor(o, t) {
        this.__proto__ = o;
        this.original = o;
        this.tracker = t;
    }

    error = (...variables) => {
        if (this.tracker.isEnabled && this.tracker.isLogged('ERROR')) {
            variables.forEach((variable) => {
                const data = this.tracker.getPageData();
                data.data = {
                    'message': variable,
                };
                data.level = 'JS_ERROR';
                data.events = this.tracker.storage.get('events');
                this.tracker.send(data)
            })
        }
        this.__proto__.error(...variables)
    }

    log = (...variables) => {
        if (this.tracker.isEnabled && this.tracker.isLogged('LOG')) {
            variables.forEach((variable) => {
                const data = this.tracker.getPageData();
                data.data = {
                    'message': variable,
                };
                data.level = 'JS_LOG';
                data.events = this.tracker.storage.get('events');
                this.tracker.send(data)
            })
        }
        return this.__proto__.log(...variables)
    }

    info = (...variables) => {
        if (this.tracker.isEnabled && this.tracker.isLogged('INFO')) {
            variables.forEach((variable) => {
                const data = this.tracker.getPageData();
                data.data = {
                    'message': variable,
                };
                data.level = 'JS_INFO';
                data.events = this.tracker.storage.get('events');
                this.tracker.send(data)
            })
        }
        return this.__proto__.info(...variables)
    }

    warn = (...variables) => {
        if (this.tracker.isEnabled && this.tracker.isLogged('WARNING')) {
            variables.forEach((variable) => {
                const data = this.tracker.getPageData();
                data.data = {
                    'message': variable,
                };
                data.level = 'JS_WARNING';
                data.events = this.tracker.storage.get('events');
                this.tracker.send(data)
            })
        }
        return this.__proto__.warn(...variables)
    }

}

class HealthCheckJsTrack {
    enable = false;
    levels = [];
    key = '';
    serviceUrl = 'https://hc.mobsdev.com/api/log';
    lifetime = 2400;
    loggedEvents = [
        'onclick',
        'onchange',
        'onkeydown',
        'onload',
        'onfocus',
        'onsubmit',
        'ondbclick'
    ];

    storage = {
        read: () => {
            const data = window.localStorage.getItem('mm-tracker');
            if (data) {
                return JSON.parse(data);
            } else {
                return {};
            }
        },
        write: (data) => {
            data.timestamp = Date.now();
            window.localStorage.setItem('mm-tracker', JSON.stringify(data))
        },
        add: (key, value) => {
            const data = this.storage.read();
            data[key] = value;
            this.storage.write(data);
        },
        isset: (key) => {
            const data = this.storage.read();
            return data[key] !== undefined;
        },
        get: (key) => {
            if (this.storage.isset(key)) {
                const data = this.storage.read();
                return data[key];
            } else {
                this.storage.add(key, null)
                return this.storage.get(key);
            }
        }
    }

    constructor() {
        Object.keys(window).forEach(key => {
            if(this.loggedEvents.includes(key)){
                window.addEventListener(key.slice(2), event => {
                    this.eventLogHandler(key, event)
                })
            }
        })
    }

    eventLogHandler = (key, event) => {
        const elem = event.target;
        const itemData = {
            timestamp: Date.now(),
            type: event.type,
            url: location.href,
            target: this.calculateSelector(elem),
        }
        if (elem.value) {
            const arr = new Array(elem.value.length);
            arr.fill('*');
            itemData.value = arr.join('');
        }

        this.addEventToList(itemData)
    }

    onErrorHandler = (message, source, line, col, error) => {
        if(!this.enable) {
            return ;
        }

        if (error !== null) {
            const data = this.getPageData();
            data.message = error.message;
            data.trace =  error.stack;
            data.events = this.storage.get('events');
            data.level = 'JS_CRITICAL';
            const eventData = {
                timestamp: Date.now(),
                message: error.message,
                type: 'ERROR',
                url: location.href,
                id: ''
            };
            const id = this.addEventToList(eventData);
            this.send(data, id);
        }
    }

    addEventToList = (item) => {
        const events = this.storage.get('events');
        if (Array.isArray(events)) {
            const last = events[events.length - 1];
            let id = null;
            if (last && (last.type == item.type && last.url == item.url && last.target == item.target)) {
                events[events.length - 1] = item;
            } else {
               id = events.push(item);
            }
            this.storage.add('events', events);

            return id;
        } else {
            this.storage.add('events', [item])
            return 0;
        }

    }

    getPageData = () => {
        return {
            'agent': navigator.userAgent,
            'url': location.href,
            'user-id': this.getToken()
        };
    }

    refreshUserToken = () => {
        const lastTimestamp = this.storage.get('timestamp');
        if ((Date.now() - lastTimestamp) > (this.lifetime * 1000)) {
            this.storage.add('events', []);
        }
        const token = this.getToken();
        if (token == undefined) {
            this.setToken(this.generateToken(8));
        }
    }

    getToken = () => {
        if (this.storage.isset('tracker-user-id')) {
            return this.storage.get('tracker-user-id')
        } else {
            return undefined;
        }
    }

    generateToken = (length = 16) => {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    setToken = (value) =>  {
        this.storage.add('tracker-user-id', value)
    }

    setEventErrorId = (key, id) => {
        const events = this.storage.get('events');
        if (events[key - 1]) {
            events[key - 1]['id'] = id;
            this.storage.add('events', events);
        }
    }

    install = (config) => {
        if (config.key) {
            this.key = config.key;
        } else {
            console.warn('Autorization key is required');
            return ;
        }
        if (config.log) {
            if (config.levels) {
                this.levels = config.levels;
            } else {
                console.warn('Levels is required.');
                return ;
            }

            const trackedConsole = new TrackedConsole(window.console, this);
            window.console = trackedConsole;
        }
        if (config.lifetime) {
            this.lifetime = config.lifetime;
        }

        this.enable = true;
        window.onerror = this.onErrorHandler;
        this.refreshUserToken();
    }

    send = (data, id) => {
        if (!this.enable) {
            return ;
        }
        fetch(this.serviceUrl, {
            method: 'POST',
            headers: {
                'Authentication-Key': this.key,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((json) => {
                if(json.id) {
                    this.setEventErrorId(id, json.id);
                }
            })
            .catch((error) => {
            });
    }

    isLogged = (level) => {
        return this.levels.includes(level);
    }

    isEnabled = () => {
        return this.enable;
    }

    calculateSelector = (element) => {
        let selector = '';
        for(let current = element; current?.nodeType === 1; current = current.parentElement) {
            if(current.id) {
                selector = `#${current.id}>${selector}`;
                break;
            }
            const tag = current.tagName.toLowerCase();
            const classes = Array.from(current.classList, cls => `.${cls}`).join('');
            selector = `${tag}${classes}>${selector}`;
        }
        return selector.slice(0, -1);
    }
}

window.healthCheckTrackJs = new HealthCheckJsTrack();