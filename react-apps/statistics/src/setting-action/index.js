const NAMESPACE = 'healthcheck';

const merge = (x, y, fn) => {
    var result = {};

    Object.keys(x).forEach(function(k) {
        result[k] = x[k];
    });

    Object.keys(y).forEach(function(k) {
        result[k] = k in x ? fn(x[k], y[k]) : y[k];
    });

    return result;
}

const add = (p, q) => {
    if(typeof p === 'object' && typeof q === 'object') {
        return merge(p, q, add);
    }
    return q;
}

const addValueToLocalStorage = (value) => {
    let storageData = JSON.parse(localStorage.getItem(NAMESPACE));
    if (storageData) {
        storageData = merge(storageData, value, add);
    } else {
        storageData = value;
    }
    localStorage.setItem(NAMESPACE, JSON.stringify(storageData));
}

const setLevelColor = (level, color) => {
    const value = {
        statColor: {}
    };
    value.statColor[level] = color;
    addValueToLocalStorage(value);
}

const getLevelColor = (level) => {
    const storageData = JSON.parse(localStorage.getItem(NAMESPACE));
    return storageData && storageData.statColor && storageData.statColor[level] ? storageData.statColor[level] : false;
}

const setLevelActivity = (level, flag) => {
    const value = {
        statActive: {}
    };
    value.statActive[level] = flag;
    addValueToLocalStorage(value);
}

const getLevelActivity = (level) => {
    const storageData = JSON.parse(localStorage.getItem(NAMESPACE));
    return storageData && storageData.statActive && storageData.statActive[level] !== undefined ? storageData.statActive[level] : undefined;
}


export {
    setLevelColor,
    getLevelColor,
    setLevelActivity,
    getLevelActivity
}